<?php

namespace oauth2\grants;

use oauth2\repositories\SessionRepository;
use repositories\UserRepository;
use oauth2\http\RequestInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Абстрактный способ авторизации пользователя при oauth2
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 */
abstract class AbstractGrant
{
    /**
     * @var SessionRepository
     */
    protected $sessionRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param SessionRepository $sessionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
    }

    /**
     * @throws UnauthorizedHttpException
     */
    protected function throwUnauthorizedHttpException()
    {
        throw new UnauthorizedHttpException('Authorization required');
    }
}