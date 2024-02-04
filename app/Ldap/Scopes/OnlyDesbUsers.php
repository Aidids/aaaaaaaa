<?php

namespace App\Ldap\Scopes;

use LdapRecord\Models\Model;
use LdapRecord\Models\Scope;
use LdapRecord\Query\Model\Builder;

class OnlyDesbUsers implements Scope
{
    /**
     * Apply the scope to the given query.
     *
     * @param Builder $query
     * @param Model   $model
     *
     * @return void
     */
    public function apply(Builder $query, Model $model)
    {
        $query->in('OU=DESB,DC=my,DC=desb');
    }
}
