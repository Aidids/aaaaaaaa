<?php

namespace App\Ldap;

use App\Models\User as DatabaseUser;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

class AttributeHandler
{
    public function handle(LdapUser $ldap, DatabaseUser $database)
    {
        $database->name = $ldap->getFirstAttribute('cn');
        $database->username = $ldap->getFirstAttribute('samaccountname');
        $database->dn = $ldap->getDn();
        $database->title = $ldap->getFirstAttribute('title') ?? 'Title unassigned';
        $database->email = $ldap->getFirstAttribute('mail');
    }
}
