<?php

namespace App\Enums;

final class Blade
{
	// Root / single files
	public const NAVIGATION_MENU = 'navigation-menu';
	public const PAGINATION = 'pagination';
	public const POLICY = 'policy';
	public const TERMS = 'terms';
	public const WELCOME = 'welcome';

	// Layouts
	public const LAYOUTS_APP = 'layouts.app';
	public const LAYOUTS_GUEST = 'layouts.guest';
	public const LAYOUTS_COMMERCIAL_HOME = 'layouts.commercial.home';
	public const LAYOUTS_DASHBOARD_HOME = 'layouts.dashboard.home';
	public const LAYOUTS_EMPLOYEE_HOME = 'layouts.employee.home';
	public const LAYOUTS_SERVICE_HOME = 'layouts.service.home';

	// Auth
	public const AUTH_CONFIRM_PASSWORD = 'auth.confirm-password';
	public const AUTH_FORGOT_PASSWORD = 'auth.forgot-password';
	public const AUTH_LOGIN = 'auth.login';
	public const AUTH_REGISTER = 'auth.register';
	public const AUTH_RESET_PASSWORD = 'auth.reset-password';
	public const AUTH_TWO_FACTOR_CHALLENGE = 'auth.two-factor-challenge';
	public const AUTH_VERIFY_EMAIL = 'auth.verify-email';

	// API
	public const API_TOKEN_MANAGER = 'api.api-token-manager';
	public const API_INDEX = 'api.index';

	// Components
	public const COMPONENTS_ACTION_MESSAGE = 'components.action-message';
	public const COMPONENTS_ACTION_SECTION = 'components.action-section';
	public const COMPONENTS_APPLICATION_LOGO = 'components.application-logo';
	public const COMPONENTS_APPLICATION_MARK = 'components.application-mark';
	public const COMPONENTS_AUTHENTICATION_CARD_LOGO = 'components.authentication-card-logo';
	public const COMPONENTS_AUTHENTICATION_CARD = 'components.authentication-card';
	public const COMPONENTS_BANNER = 'components.banner';
	public const COMPONENTS_BUTTON = 'components.button';
	public const COMPONENTS_CARDS_CARD = 'components.cards.card';
	public const COMPONENTS_CHECKBOX = 'components.checkbox';
	public const COMPONENTS_CONFIRMATION_MODAL = 'components.confirmation-modal';
	public const COMPONENTS_CONFIRMS_PASSWORD = 'components.confirms-password';
	public const COMPONENTS_DANGER_BUTTON = 'components.danger-button';
	public const COMPONENTS_DATEPICKER = 'components.datepicker';
	public const COMPONENTS_DIALOG_MODAL = 'components.dialog-modal';
	public const COMPONENTS_DROPDOWN_LINK = 'components.dropdown-link';
	public const COMPONENTS_DROPDOWN = 'components.dropdown';
	public const COMPONENTS_FORM_SECTION = 'components.form-section';
	public const COMPONENTS_ICON = 'components.icon';
	public const COMPONENTS_INPUT_ERROR = 'components.input-error';
	public const COMPONENTS_INPUT = 'components.input';
	public const COMPONENTS_LABEL = 'components.label';
	public const COMPONENTS_LAYOUTS = 'components.layouts';
	public const COMPONENTS_LOCALE_SWITCHER = 'components.locale-switcher';
	public const COMPONENTS_MODAL_CARD = 'components.modal.modal-card';
	public const COMPONENTS_MODAL_INFO = 'components.modal.modal-info';
	public const COMPONENTS_MODAL = 'components.modal.modal';
	public const COMPONENTS_MONEY_INPUT = 'components.money-input';
	public const COMPONENTS_NAV_LINK = 'components.nav-link';
	public const COMPONENTS_RESPONSIVE_NAV_LINK = 'components.responsive-nav-link';
	public const COMPONENTS_SECONDARY_BUTTON = 'components.secondary-button';
	public const COMPONENTS_SECTION_BORDER = 'components.section-border';
	public const COMPONENTS_SECTION_TITLE = 'components.section-title';
	public const COMPONENTS_SWITCHABLE_TEAM = 'components.switchable-team';
	public const COMPONENTS_VALIDATION_ERRORS = 'components.validation-errors';

	// Profile
	public const PROFILE_DELETE_USER_FORM = 'profile.delete-user-form';
	public const PROFILE_LOGOUT_OTHER_BROWSERS = 'profile.logout-other-browser-sessions-form';
	public const PROFILE_SHOW = 'profile.show';
	public const PROFILE_TWO_FACTOR_AUTH = 'profile.two-factor-authentication-form';
	public const PROFILE_UPDATE_PASSWORD = 'profile.update-password-form';
	public const PROFILE_UPDATE_INFORMATION = 'profile.update-profile-information-form';

	// Team invitations
	public const TEAM_INVITATIONS_SHOW = 'team-invitations.show';

	// Teams
	public const TEAMS_CREATE_TEAM_FORM = 'teams.create-team-form';
	public const TEAMS_CREATE = 'teams.create';
	public const TEAMS_DELETE_TEAM_FORM = 'teams.delete-team-form';
	public const TEAMS_SHOW = 'teams.show';
	public const TEAMS_TEAM_MEMBER_MANAGER = 'teams.team-member-manager';
	public const TEAMS_UPDATE_TEAM_NAME_FORM = 'teams.update-team-name-form';

	// Livewire - commercial
	public const LIVEWIRE_COMMERCIAL_HOME = 'livewire.commercial.home';
	public const LIVEWIRE_COMMERCIAL_SERVICES_PRODUCTS = 'livewire.commercial.services_products';

	// Livewire - companies
	public const LIVEWIRE_COMPANIES_HOME = 'livewire.companies.home';
	public const LIVEWIRE_COMPANIES_CREATE = 'livewire.companies.create';
	public const LIVEWIRE_COMPANIES_INDEX = 'livewire.companies.index';

	// Livewire - components
	public const LIVEWIRE_COMPONENTS_CARD = 'livewire.components.card';

	// Livewire - customer (novo padrão)
	public const LIVEWIRE_CUSTOMER_INDEX = 'livewire.customer.index-customer';
	public const LIVEWIRE_CUSTOMER_FORM = 'livewire.customer.customer-form-component';

	// Livewire - dashboard
	public const LIVEWIRE_DASHBOARD_HOME = 'livewire.dashboard.home';

	// Livewire - employee
	public const LIVEWIRE_EMPLOYEE_FORM = 'livewire.employee.form';
	public const LIVEWIRE_EMPLOYEE_INDEX = 'livewire.employee.index-employee';

	// Livewire - service
	public const LIVEWIRE_SERVICE_CONSUMPTION = 'livewire.service.consumption';
	public const LIVEWIRE_SERVICE_SUMMARY_COMMERCIAL = 'livewire.service.summary-commercial';
	public const LIVEWIRE_SERVICE_ANY_SEARCH = 'livewire.service.any-search';
	public const LIVEWIRE_SERVICE_FORM = 'livewire.service.form';
	public const LIVEWIRE_SERVICE_INDEX = 'livewire.service.index';
	public const LIVEWIRE_SERVICE_REGISTER = 'livewire.service.register-service';
	public const LIVEWIRE_SERVICE_INDEX_EMPLOYEE = 'livewire.service.index-employee';
	public const LIVEWIRE_SERVICE_HIERARCHY_MANAGER = 'livewire.service.hierarchy-manager';
	public const LIVEWIRE_SERVICE_PACKAGES = 'livewire.service.services-pagination';
	public const LIVEWIRE_SERVICE_PACKAGES_INDEX = 'livewire.service.index';

	// Livewire - product / packages
	public const LIVEWIRE_SERVICE_ADD_ITEM_PACKAGE = 'livewire.service.add-item-package';
	public const LIVEWIRE_SERVICE_FORM_PACKAGE = 'livewire.service.form-package';
	public const LIVEWIRE_SERVICE_FORM_PRODUCT = 'livewire.service.form-product';
	public const LIVEWIRE_SERVICE_FORM_PRODUCTS = 'livewire.service.form-products';
	public const LIVEWIRE_SERVICE_FORM_SERVICE = 'livewire.service.form-service';
	public const LIVEWIRE_SERVICE_ITEM_PACKAGE = 'livewire.service.item-package';
	public const LIVEWIRE_SERVICE_ITEMS_PACKAGE = 'livewire.service.items-package';
	public const LIVEWIRE_SERVICE_PACKAGES_PAGINATION = 'livewire.service.packages-pagination';
	public const LIVEWIRE_SERVICE_PRODUCTS_PAGINATION = 'livewire.service.products-pagination';
	public const LIVEWIRE_SERVICE_SERVICES_PAGINATION = 'livewire.service.services-pagination';
    public const LIVEWIRE_COMPONENTS_ANY_SEARCH = 'livewire.components.any-search';

	// Wire elements modal vendor
	public const VENDOR_WIRE_ELEMENTS_MODAL = 'vendor.wire-elements-modal.modal';
}
