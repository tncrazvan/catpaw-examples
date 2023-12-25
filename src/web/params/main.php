<?php

use CatPaw\Attributes\Entry;
use CatPaw\Attributes\Service;
use function CatPaw\uuid;

use const CatPaw\Web\APPLICATION_JSON;
use CatPaw\Web\Attributes\Produces;
use CatPaw\Web\Attributes\ProducesPage;

use CatPaw\Web\Attributes\Summary;
use function CatPaw\Web\ok;
use CatPaw\Web\Page;

use CatPaw\Web\Server;
use const CatPaw\Web\TEXT_PLAIN;

class Account {
    function __construct(
        public string $username,
        public string $name,
    ) {
    }
}

#[Service]
class AccountService {
    /** @var array<Account> */
    private array $accounts = [];

    #[Entry] function start() {
        $this->accounts[] = new Account(username: uuid(), name: 'Raz');
        $this->accounts[] = new Account(username: uuid(), name: 'Marta');
        $this->accounts[] = new Account(username: uuid(), name: 'Tom');
        $this->accounts[] = new Account(username: uuid(), name: 'Clore');
        $this->accounts[] = new Account(username: uuid(), name: 'Brahim');
        $this->accounts[] = new Account(username: uuid(), name: 'Oscar');
    }

    /**
     * Find accounts by name.
     * @param  Page           $page
     * @param  string         $name
     * @return array<Account>
     */
    function findByName(Page $page, string $name):array {
        $result = array_filter($this->accounts, fn (Account $account) => strtolower($account->name) === strtolower($name));
        return array_slice($result, $page->start, $page->size);
    }

    /**
     * Find all accounts (paginated).
     * @param  Page  $page
     * @return array
     */
    function findAll(Page $page):array {
        return array_slice($this->accounts, $page->start, $page->size);
    }
}

/**
 * 
 * @param AccountService $accountService
 * @param string         $name
 */
#[Summary('Find accounts by their name.')]
#[ProducesPage(
    className: Account::class,
    contentType: APPLICATION_JSON,
    example: new Account( username: "b5e6a138-0d9e-42d4-aa2c-db33a4fcec37", name: "user1" )
)]
function findAccountsByName(AccountService $accountService, Page $page, string $name) {
    $items = $accountService->findByName($page, $name);
    return ok($items)->page($page);
}

#[Produces('string', TEXT_PLAIN, "Account user1 has been activated.")]
#[Summary('Toggle an account, activating or deactivating it.')]
function toggleAccountByUsername(string $username, bool $active) {
    if ($active) {
        return "Account $username has been activated.";
    }
    return "Account $username has been deactivated.";
}

#[ProducesPage(
    className: Account::class,
    contentType: APPLICATION_JSON,
    example: new Account( username: "b5e6a138-0d9e-42d4-aa2c-db33a4fcec37", name: "user1" )
)]
#[Summary('Find all accounts.')]
function findAll(AccountService $accountService, Page $page) {
    return ok($accountService->findAll($page))->page($page);
}

function main(): void {
    $server = Server::create( www:'./public' );
    $server->router->get('/account/by-name/{name}', findAccountsByName(...));
    $server->router->get('/account/{username}/toggle/{active}', toggleAccountByUsername(...));
    $server->router->get('/accounts', findAll(...));
    showSwaggerUI($server);
    $server->start();
}