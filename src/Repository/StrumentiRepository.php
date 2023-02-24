<?php

namespace App\Repository;

use App\Entity\Strumenti;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Strumenti>
 *
 * @method Strumenti|null find($id, $lockMode = null, $lockVersion = null)
 * @method Strumenti|null findOneBy(array $criteria, array $orderBy = null)
 * @method Strumenti[]    findAll()
 * @method Strumenti[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StrumentiRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Strumenti::class);
    }

    public function add(Strumenti $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Strumenti $entity, bool $flush = false): void
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
        if (!$user instanceof Strumenti) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

//    /**
//     * @return Strumenti[] Returns an array of Strumenti objects
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

//    public function findOneBySomeField($value): ?Strumenti
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



    public function findStrumenti($filtri) {
        $qb = $this->createQueryBuilder('a');

        if (isset($filtri['s']) && $filtri['s'] != "") {
            $search = preg_replace("/'/","''", $filtri['s']);
            $qb->andWhere("a.nome LIKE '%". $search ."%' OR a.codice LIKE '%". $search ."%'");
        }

        if (isset($filtri['ord']) && $filtri['ord'] != "") {
            switch ($filtri['ord']) {
                case "codiceAsc":
                    $qb->orderBy('a.codice', 'ASC');
                    break;
                case "codiceDesc":
                    $qb->orderBy('a.codice', 'DESC');
                    break;
                case "nomeAsc":
                    $qb->orderBy('a.nome', 'ASC');
                    break;
                case "nomeDesc":
                    $qb->orderBy('a.nome', 'DESC');
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
