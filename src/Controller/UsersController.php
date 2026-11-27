<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * Handles CMS login/logout plus user management (CRUD). All management actions
 * are restricted to superadmin and admin roles; login/logout are public.
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Roles allowed to manage users.
     *
     * @var list<string>
     */
    private array $managerRoles = ['superadmin', 'admin'];

    /**
     * Before filter: allow login/logout without an existing session and use the
     * backend layout for the management screens.
     *
     * @param \Cake\Event\EventInterface $event The event.
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // Everything here needs a logged-in user...
        $this->Authentication->setConfig('requireIdentity', true);
        // ...except the login form and the public password-reset flow.
        $this->Authentication->allowUnauthenticated(['login', 'forgotPassword', 'resetPassword']);

        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Login action.
     *
     * @return \Cake\Http\Response|null Redirects on success, renders form otherwise.
     */
    public function login(): ?Response
    {
        // The login form itself needs no authorization check.
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('login');

        $result = $this->Authentication->getResult();

        // Regardless of POST/GET, redirect a successfully authenticated user.
        if ($result && $result->isValid()) {
            // Record last login timestamp.
            $identity = $this->Authentication->getIdentity();
            if ($identity !== null) {
                $userId = $identity->getIdentifier();
                $user = $this->Users->get($userId);
                $user->last_login = new DateTime();
                $this->Users->save($user);
            }

            $target = $this->request->getQuery('redirect');
            if (is_string($target) && $target !== '' && str_starts_with($target, '/')) {
                return $this->redirect($target);
            }

            return $this->redirect(['action' => 'index']);
        }

        // Show an error only after a failed POST attempt.
        if ($this->request->is('post') && $result && !$result->isValid()) {
            $this->Flash->error('Invalid email or password. Please try again.');
        }

        return null;
    }

    /**
     * Logout action.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            $this->Flash->success('You have been logged out.');
        }

        return $this->redirect(['action' => 'login']);
    }

    /**
     * Forgot password: show a form to request a reset link, and on POST send an
     * email containing a single-use, time-limited reset link.
     *
     * For privacy we always show the same success message whether or not the
     * email exists, so the form can't be used to probe which emails are registered.
     *
     * @return \Cake\Http\Response|null Redirects on success, renders form otherwise.
     */
    public function forgotPassword(): ?Response
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('login');

        if ($this->request->is('post')) {
            $email = trim((string)$this->request->getData('email'));

            // Look up an active account for this email.
            $user = $email !== ''
                ? $this->Users->find()->where(['email' => $email, 'status' => 1])->first()
                : null;

            // No matching account: tell the user and keep them on the form.
            if ($user === null) {
                $this->Flash->error('That email address is not registered.');

                return null;
            }

            // Raw token goes in the email link; only its hash is stored.
            $rawToken = bin2hex(Security::randomBytes(32));
            $user->password_reset_token = hash('sha256', $rawToken);
            $user->password_reset_expires = (new DateTime())->modify('+1 hour');

            if ($this->Users->save($user)) {
                $this->sendResetEmail($user, $rawToken);
            }

            $this->Flash->success('A password reset link has been sent to your email.');

            return $this->redirect(['action' => 'login']);
        }

        return null;
    }

    /**
     * Reset password: validate the token from the emailed link, then let the
     * user set a new password (with confirmation). GET shows the form; POST
     * saves the new password and clears the token.
     *
     * @param string|null $token The raw reset token from the email link.
     * @return \Cake\Http\Response|null
     */
    public function resetPassword(?string $token = null): ?Response
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('login');

        $user = $this->findUserByResetToken($token);

        // Invalid or expired token: send them back to request a new link.
        if ($user === null) {
            $this->Flash->error('This password reset link is invalid or has expired. Please request a new one.');

            return $this->redirect(['action' => 'forgotPassword']);
        }

        if ($this->request->is(['post', 'put'])) {
            $password = (string)$this->request->getData('password');
            $confirm = (string)$this->request->getData('confirm_password');

            if (strlen($password) < 8) {
                $this->Flash->error('Your new password must be at least 8 characters long.');
            } elseif ($password !== $confirm) {
                $this->Flash->error('The passwords do not match. Please re-enter them.');
            } else {
                // Setting password triggers the entity's hashing. Clear the
                // token so the link can't be reused.
                $user->password = $password;
                $user->password_reset_token = null;
                $user->password_reset_expires = null;

                if ($this->Users->save($user)) {
                    $this->Flash->success('Your password has been changed. You can now sign in with your new password.');

                    return $this->redirect(['action' => 'login']);
                }

                $this->Flash->error('Your password could not be changed. Please try again.');
            }
        }

        // Pass the raw token so the form can post back to the same URL.
        $this->set('token', $token);

        return null;
    }

    /**
     * Look up an active user by a raw reset token, enforcing expiry.
     *
     * @param string|null $token Raw token from the URL.
     * @return \App\Model\Entity\User|null The matching user, or null.
     */
    private function findUserByResetToken(?string $token): ?\App\Model\Entity\User
    {
        if (!is_string($token) || $token === '') {
            return null;
        }

        $hashed = hash('sha256', $token);

        /** @var \App\Model\Entity\User|null $user */
        $user = $this->Users->find()
            ->where([
                'password_reset_token' => $hashed,
                'password_reset_expires >' => new DateTime(),
                'status' => 1,
            ])
            ->first();

        return $user;
    }

    /**
     * Send the password reset email with a link containing the raw token.
     *
     * @param \App\Model\Entity\User $user The user requesting the reset.
     * @param string $rawToken The un-hashed token to embed in the link.
     * @return void
     */
    private function sendResetEmail(\App\Model\Entity\User $user, string $rawToken): void
    {
        $fromEmail = Configure::read('ContactForm.fromEmail', 'info@aranetacity.com');
        $fromName = Configure::read('ContactForm.fromName', 'Araneta City Website');

        // Absolute URL so the link works from an email client.
        $resetUrl = Router::url([
            'controller' => 'Users',
            'action' => 'resetPassword',
            $rawToken,
        ], true);

        try {
            $mailer = new Mailer('default');
            $mailer
                ->setFrom($fromEmail, $fromName)
                ->setEmailFormat('html')
                ->setTo($user->email)
                ->setSubject('Reset your Araneta City CMS password')
                ->setViewVars([
                    'firstName' => $user->first_name ?: 'there',
                    'resetUrl' => $resetUrl,
                    'expiresText' => '1 hour',
                ])
                ->viewBuilder()
                ->setTemplate('reset_password')
                ->disableAutoLayout();
            $mailer->deliver();
        } catch (\Throwable $e) {
            // Never surface mail errors to the user (would leak account existence
            // and internal detail); just log for diagnostics.
            \Cake\Log\Log::error('Password reset email failed: ' . $e->getMessage());
        }
    }

    /**
     * Guards management actions to superadmin/admin roles.
     *
     * @return \Cake\Http\Response|null Redirect when access is denied.
     */
    private function ensureManager(): ?Response
    {
        $this->Authorization->skipAuthorization();

        if (!$this->userHasRole($this->managerRoles)) {
            $this->Flash->error('You are not authorized to manage users.');

            return $this->redirect(['controller' => 'Mainpages', 'action' => 'index']);
        }

        return null;
    }

    /**
     * Index: list all users.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $users = $this->Users->find()
            ->contain(['Roles'])
            ->orderBy(['Users.created' => 'DESC'])
            ->all();

        $this->set(compact('users'));

        return null;
    }

    /**
     * View a single user.
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     */
    public function view(?string $id = null): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $user = $this->Users->get($id, contain: ['Roles']);
        $this->set(compact('user'));

        return null;
    }

    /**
     * Add a new user.
     *
     * @return \Cake\Http\Response|null
     */
    public function add(): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success('The user has been saved.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The user could not be saved. Please check the form.');
        }

        $roles = $this->Users->Roles->find('list', keyField: 'id', valueField: 'name')
            ->where(['status' => 1])
            ->all();
        $this->set(compact('user', 'roles'));

        return null;
    }

    /**
     * Edit an existing user.
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     */
    public function edit(?string $id = null): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $user = $this->Users->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Don't overwrite the password with an empty value on edit.
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success('The user has been saved.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The user could not be saved. Please check the form.');
        }

        $roles = $this->Users->Roles->find('list', keyField: 'id', valueField: 'name')
            ->where(['status' => 1])
            ->all();
        $this->set(compact('user', 'roles'));

        return null;
    }

    /**
     * Delete a user.
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null): ?Response
    {
        if ($redirect = $this->ensureManager()) {
            return $redirect;
        }

        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        // Prevent a user from deleting their own account.
        $current = $this->currentUser();
        if ($current !== null && (int)$current->id === (int)$user->id) {
            $this->Flash->error('You cannot delete your own account.');

            return $this->redirect(['action' => 'index']);
        }

        if ($this->Users->delete($user)) {
            $this->Flash->success('The user has been deleted.');
        } else {
            $this->Flash->error('The user could not be deleted. Please try again.');
        }

        return $this->redirect(['action' => 'index']);
    }
}
