<?php

namespace App\Doctrine\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

#[AsDoctrineListener(event: ToolEvents::postGenerateSchema, connection: 'default')]
class FixPostgresDefaultSchemaListener
{
    /**
     * @throws Exception
     * @throws SchemaException
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $schemaManager = $args
            ->getEntityManager()
            ->getConnection()
            ->createSchemaManager();

        if(!$schemaManager instanceof  PostgreSQLSchemaManager){
            return;
        }

        $schema = $args->getSchema();

        foreach($schemaManager->listSchemaNames() as $namespace){
            if(!$schema->hasNamespace($namespace)){
                $schema->createNamespace($namespace);
            }
        }
    }
}