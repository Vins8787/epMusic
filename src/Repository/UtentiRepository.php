<?php

namespace App\Repository;

use App\Entity\Utenti;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utenti>
 *
 * @method Utenti|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utenti|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utenti[]    findAll()
 * @method Utenti[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtentiRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utenti::class);
    }

    public function add(Utenti $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Utenti $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utenti) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

//    /**
//     * @return Utenti[] Returns an array of Utenti objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utenti
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



    public function findUtenti($filtri) {
        $qb = $this->createQueryBuilder('a');

        if (isset($filtri['s']) && $filtri['s'] != "") {
            $search = preg_replace("/'/","''", $filtri['s']);
            $qb->andWhere("a.nome LIKE '%". $search ."%' OR a.cognome LIKE '%". $search ."%' OR a.userIdentifier LIKE '%". $search ."%' OR a.ruolo LIKE '%". $search ."%' OR a.company LIKE '%". $search ."%'");
        }

        if (isset($filtri['ord']) && $filtri['ord'] != "") {
            switch ($filtri['ord']) {
                case "emailAsc":
                    $qb->orderBy('a.userIdentifier', 'ASC');
                    break;
                case "emailDesc":
                    $qb->orderBy('a.userIdentifier', 'DESC');
                    break;
                case "nomeAsc":
                    $qb->orderBy('a.nome', 'ASC');
                    break;
                case "nomeDesc":
                    $qb->orderBy('a.nome', 'DESC');
                    break;
                case "cognomeAsc":
                    $qb->orderBy('a.cognome', 'ASC');
                    break;
                case "cognomeDesc":
                    $qb->orderBy('a.cognome', 'DESC');
                    break;
            }
        } else { //default
            $qb->orderBy('a.nome', 'ASC');
        }


        //print_r($qb->getDql());
        //print_r($qb->getQuery()->getSql());
        return $qb->getQuery()->getResult();
    }



}
