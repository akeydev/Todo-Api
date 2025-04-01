<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Todo;
use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class GetTodoProvider implements ProviderInterface
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security, private readonly Pagination $pagination)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator| null
    {
        $user = $this->security->getUser();
        
        if ($operation instanceof GetCollection && $user instanceof User) {
            $repository = $this->entityManager->getRepository(Todo::class);
            if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
                $queryBuilder = $repository->createQueryBuilder('t');
            } else {
                $queryBuilder = $repository->createQueryBuilder('t')
                    ->where('t.createdBy = :user')
                    ->setParameter('user', $user->getId());
            }

            if (isset($context['filters'])) {
                foreach ($context['filters'] as $field => $value) {
                    if ($field === 'page' || $field === 'itemsPerPage' || $field === 'pagination') {
                        continue;
                    }
                    $queryBuilder->andWhere(sprintf('t.%s like :%s', $field, $field))
                        ->setParameter($field, '%' . $value . '%');
                }
            }
           
            $page = $context['filters']['page'] ?? 1;
            $itemsPerPage = $context['filters']['itemsPerPage'] ?? 10;
           
            $queryBuilder->addCriteria(
                    Criteria::create()
                        ->setFirstResult(($page - 1) * $itemsPerPage)
                        ->setMaxResults($itemsPerPage)
            );
            $data = new DoctrinePaginator($queryBuilder->getQuery());
            return new Paginator($data);
        }
        return null;
    }
}
