<?php

namespace pxlrbt\FilamentActivityLog\Pages\Concerns;

use Filament\Tables\Enums\PaginationMode;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait CanPaginate
{
    /**
     * @var int | string | null
     */
    public $recordsPerPage = null;

    protected int|string|null $defaultRecordsPerPageSelectOption = null;

    public function updatedRecordsPerPage(): void
    {
        session()->put([
            $this->getPerPageSessionKey() => $this->getRecordsPerPage(),
        ]);

        $this->resetLivewirePage();
    }

    protected function paginateQuery(Builder $query): Paginator|CursorPaginator
    {
        $perPage = $this->getRecordsPerPage();

        $mode = $this->getPaginationMode();

        if ($mode === PaginationMode::Simple) {
            return $query->simplePaginate(
                perPage: ($perPage === 'all') ? $query->toBase()->getCountForPagination() : $perPage,
                pageName: $this->getPaginationPageName(),
            );
        }

        if ($mode === PaginationMode::Cursor) {
            return $query->cursorPaginate(
                perPage: ($perPage === 'all') ? $query->toBase()->getCountForPagination() : $perPage,
                cursorName: $this->getPaginationPageName(),
            );
        }

        $total = $query->toBase()->getCountForPagination();

        /** @var LengthAwarePaginator $records */
        $records = $query->paginate(
            perPage: ($perPage === 'all') ? $total : $perPage,
            pageName: $this->getPaginationPageName(),
            total: $total,
        );

        return $records->onEachSide(0);
    }

    public function getRecordsPerPage(): int|string|null
    {
        return $this->recordsPerPage;
    }

    public function getTablePage(): int
    {
        return $this->getPage($this->getPaginationPageName());
    }

    public function getDefaultRecordsPerPageSelectOption(): int|string
    {
        $option = session()->get(
            $this->getPerPageSessionKey(),
            $this->defaultRecordsPerPageSelectOption,
        );

        $pageOptions = $this->getRecordsPerPageSelectOptions();

        if (in_array($option, $pageOptions)) {
            return $option;
        }

        session()->remove($this->getPerPageSessionKey());

        return $pageOptions[0];
    }

    public function getPaginationPageName(): string
    {
        return 'recordsPerPage';
    }

    public function getPerPageSessionKey(): string
    {
        $name = md5($this::class);

        return "pages.{$name}_per_page";
    }

    /**
     * @return array<int | string> | null
     */
    protected function getRecordsPerPageSelectOptions(): ?array
    {
        return [10, 25, 50];
    }
}
