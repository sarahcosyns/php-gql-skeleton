<?php

namespace Vertuoza\Repositories\Settings\Collaborators;

use Vertuoza\Repositories\Database\QueryBuilder;
use Vertuoza\Repositories\Settings\Collaborators\Models\CollaboratorMapper;
use Vertuoza\Repositories\Settings\Collaborators\Models\CollaboratorModel;

use function React\Async\async;

class CollaboratorRepository
{
    public function __construct(
        private QueryBuilder $database
    ) {
    }

    protected function getQueryBuilder()
    {
        return $this->database->getConnection()->table(CollaboratorModel::getTableName());
    }

    public function findMany(string $tenantId)
    {
        return async(
            fn () => $this->getQueryBuilder()
                ->whereNull('deleted_at')
                ->where(
                    CollaboratorModel::getTenantColumnName(),
                    '=',
                    $tenantId
                )
                ->get()
                ->map(function ($row) {
                    return CollaboratorMapper::modelToEntity(
                        CollaboratorModel::fromStdclass($row)
                    );
                })
        )();
    }
}
