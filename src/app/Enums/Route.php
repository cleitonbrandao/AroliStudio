<?php

namespace App\Enums;

final class Route
{
    // Invitations
    const WEB_TEAM_INVITATIONS_ACCEPT = 'team-invitations.accept';

    // Locale
    const WEB_LOCALE_CHANGE = 'locale.change';
    const WEB_LOCALE_CURRENT = 'locale.current';

    // Companies
    const WEB_COMPANIES_INDEX = 'companies.index';
    const WEB_COMPANIES_CREATE = 'companies.create';
    const WEB_COMPANIES_HIERARCHY = 'companies.hierarchy';

    // Dashboard / root
    const WEB_ROOT_DASHBOARD_HIERARCHY = 'root.dashboard.hierarchy';

    // Employee
    const WEB_ROOT_EMPLOYEE = 'root.employee';
    const WEB_ROOT_EMPLOYEE_INDEX = 'root.employee.index';

    // Costumer
    const WEB_ROOT_COSTUMER = 'root.costumer';
    const WEB_ROOT_COSTUMER_INDEX = 'root.costumer.index';

    // Negotiable / services
    const WEB_ROOT_NEGOTIABLE = 'root.negotiable';
    const WEB_ROOT_PRODUCT_INDEX = 'root.product.index';

    // Forms (root.form.*)
    const WEB_ROOT_FORM_EMPLOYEE = 'root.form.employee';
    const WEB_ROOT_FORM_COSTUMER = 'root.form.costumer';
    const WEB_ROOT_FORM_SERVICE = 'root.form.service';
    const WEB_ROOT_FORM_PRODUCT = 'root.form.product';
    const WEB_ROOT_FORM_PACKAGE = 'root.form.package';

    // Commercial
    const WEB_ROOT_COMMERCIAL_INDEX = 'root.commercial.index';
    const WEB_ROOT_COMMERCIAL_SUMMARY = 'root.commercial.summary';
    const WEB_ROOT_COMMERCIAL_CONSUMPTION = 'root.commercial.consumption';

    // Register actions
    const WEB_ROOT_REGISTER_COSTUMER = 'root.register.costumer';
    const WEB_ROOT_REGISTER_SERVICE = 'root.register.service';
    const WEB_ROOT_REGISTER_PACKAGE = 'root.register.package';
    const WEB_ROOT_REGISTER_ENTERPRISE = 'root.register.enterprise';

    // Update actions
    const WEB_ROOT_UPDATE_COSTUMER = 'root.update.costumer';
    const WEB_ROOT_UPDATE_PRODUCT = 'root.update.product';

    // Delete actions
    const WEB_ROOT_DELETE_PRODUCT = 'root.delete.product';

}
