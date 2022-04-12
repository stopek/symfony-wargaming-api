<?php

namespace App\Repository;

use App\Entity\User;
use App\Factory\PlayerFactory;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ServerBag;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PlayerFactory $playerFactory)
    {
        parent::__construct($registry, User::class);
    }

    public function createUserFromInputBag(PlayerRepository $playerRepository, InputBag $input, ServerBag $server): string
    {
        $access_token = $input->get('access_token');
        $account_id = (int)$input->get('account_id');
        $live_token = $input->get('live_access_token');
        $psn_token = $input->get('psn_access_token');
        $expiry = $input->get('expires_at');
        $user_agent = $server->get('HTTP_USER_AGENT');

        $token = Uuid::uuid3(Uuid::NAMESPACE_URL, $access_token . '-' . $account_id . '-' . Uuid::uuid4());

        $user = $this->findOneBy([
                'account_id' => $account_id,
                'wg_api_token' => $access_token
            ]) ?? new User();

        if (!$user->getId()) {
            $player = $playerRepository->findOneBy([
                    'id' => $account_id
                ]) ?? null;

            if (null === $player) {
                $player = $this->playerFactory->createPlayer($account_id, strlen($psn_token) > 0);
            }

            $user->setPlayer($player);
        }

        $user
            ->setAccountId($account_id)
            ->setWgApiToken($access_token)
            ->setApiToken($token)
            ->setExpiry($expiry)
            ->setLiveToken($live_token)
            ->setUserAgent($user_agent)
            ->setPsnToken($psn_token);

        $this->_em->persist($user);
        $this->_em->flush();

        return $token;
    }

    public function logOutByToken(string $token)
    {
        $user = $this->getUserToLogIn($token);

        if (null !== $user) {
            $user->setDeletedAt(new DateTime());
            $user->setApiToken(null);
            $user->setWgApiToken(null);
            $user->setExpiry(null);
            $user->setPsnToken(null);
            $user->setLiveToken(null);

            $this->_em->persist($user);
            $this->_em->flush($user);
        }
    }

    /**
     * @param string $token
     * @return ?User
     */
    public function getUserToLogIn(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.api_token = :token')
            ->andWhere('u.deleted_at IS NULL')
            ->andWhere('u.expired_at IS NULL')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
