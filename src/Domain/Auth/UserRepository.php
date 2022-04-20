<?php

namespace App\Domain\Auth;

use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use League\OAuth2\Client\Provider\{GoogleUser, GithubResourceOwner};
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\{
    PasswordAuthenticatedUserInterface,
    PasswordUpgraderInterface
};
use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class UserRepository extends ServiceEntityRepository implements
    PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param User $entity
     * @param bool $flush
     * @return void
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param User $entity
     * @param bool $flush
     * @return void
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword
    ): void {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    get_class($user)
                )
            );
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOrCreateFromGoogleOauth(
        GoogleUser $googleUser
    ): User|null {
        try {
            $user = $this->createQueryBuilder("u")
                ->where("u.googleID = :googleID")
                ->orWhere("u.email = :email")
                ->setParameters([
                    "email" => $googleUser->getEmail(),
                    "googleID" => $googleUser->getId(),
                ])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }
        if (isset($user) && $user instanceof User) {
            $data = $googleUser->toArray();
            if (
                $user->getGoogleID() === null &&
                $data["email_verified"] === true
            ) {
                try {
                    $user
                        ->setGoogleID($googleUser->getId())
                        ->setIsVerified(1)
                        ->setUpdatedAt(
                            new DateTime(
                                "now",
                                new DateTimeZone("Europe/Paris")
                            )
                        );
                } catch (Exception) {
                }
                $this->_em->persist($user);
                $this->_em->flush();
            }
            return $user;
        }
        $user = (new User())
            ->setUsername(
                $googleUser->getFirstName() . $googleUser->getLastName()
            )
            ->setGoogleID($googleUser->getId())
            ->setEmail($googleUser->getEmail())
            ->setIsVerified(1);
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }

    public function findOrCreateFromGithubOauth(
        GithubResourceOwner $githubUser
    ): User|null {
        try {
            $user = $this->createQueryBuilder("u")
                ->where("u.githubID = :githubID")
                ->orWhere("u.email = :email")
                ->setParameters([
                    "email" => $githubUser->getEmail(),
                    "githubID" => $githubUser->getId(),
                ])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }
        if (isset($user) && $user instanceof User) {
            $data = $githubUser->toArray();
            dump($data);
            if (
                $user->getGithubID() === null &&
                $data["email"] === $user->getEmail()
            ) {
                try {
                    $user
                        ->setGithubID($githubUser->getId())
                        ->setIsVerified(1)
                        ->setUpdatedAt(
                            new DateTime(
                                "now",
                                new DateTimeZone("Europe/Paris")
                            )
                        );
                } catch (Exception) {
                }
                $this->_em->persist($user);
                $this->_em->flush();
            }
            return $user;
        }
        $user = (new User())
            ->setUsername($githubUser->getNickname())
            ->setGoogleID($githubUser->getId())
            ->setEmail($githubUser->getEmail())
            ->setIsVerified(1);
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }
}
