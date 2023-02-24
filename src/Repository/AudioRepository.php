<?php

namespace App\Repository;

use App\Entity\Audio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Audio>
 *
 * @method Audio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Audio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Audio[]    findAll()
 * @method Audio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AudioRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Audio::class);
    }

    public function add(Audio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Audio $entity, bool $flush = false): void
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
        if (!$user instanceof Audio) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

//    /**
//     * @return Audio[] Returns an array of Audio objects
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

//    public function findOneBySomeField($value): ?Audio
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



    public function findAudio($filtri) {
        $qb = $this->createQueryBuilder('a');

        if (isset($filtri['s']) && $filtri['s'] != "") {
            $search = preg_replace("/'/","''", $filtri['s']);
            $qb->andWhere("a.titolo LIKE '%". $search ."%' OR a.codice LIKE '%". $search ."%' OR a.filmSerieNome LIKE '%". $search ."%'");
        }

        if (isset($filtri['genere']) && $filtri['genere'] != "") {
            $arrayGenere = implode("," , $filtri['genere']);
            $qb->leftJoin('a.audioGenere', 'ag');
            $qb->andWhere("ag.id IN ($arrayGenere)");
        }
        if (isset($filtri['feeling']) && $filtri['feeling'] != "") {
            $arrayFeeling = implode("," , $filtri['feeling']);
            $qb->leftJoin('a.audioFeeling', 'af');
            $qb->andWhere("af.id IN ($arrayFeeling)");
        }
        if (isset($filtri['strumenti']) && $filtri['strumenti'] != "") {
            $arrayStrumenti = implode("," , $filtri['strumenti']);
            $qb->leftJoin('a.audioStrumenti', 'st');
            $qb->andWhere("st.id IN ($arrayStrumenti)");
        }


        if (isset($filtri['serieFilm']) && $filtri['serieFilm'] != "") {
            $filmSerie = preg_replace("/'/","''", $filtri['serieFilm']);
            $qb->andWhere("a.filmSerieNome = '$filmSerie'");
        }



        if (isset($filtri['ord']) && $filtri['ord'] != "") {
            switch ($filtri['ord']) {
                case "codiceAsc":
                    $qb->orderBy('a.codice', 'ASC');
                    break;
                case "codiceDesc":
                    $qb->orderBy('a.codice', 'DESC');
                    break;
                case "titoloAsc":
                    $qb->orderBy('a.titolo', 'ASC');
                    break;
                case "titoloDesc":
                    $qb->orderBy('a.titolo', 'DESC');
                    break;
            }
        } else { //default
            $qb->orderBy('a.titolo', 'ASC');
        }


            //print_r($qb->getDql());
        //print_r($qb->getQuery()->getSql());
        return $qb->getQuery()->getResult();
    }



}
