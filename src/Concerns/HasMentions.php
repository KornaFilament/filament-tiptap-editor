<?php

namespace FilamentTiptapEditor\Concerns;

use Closure;
use FilamentTiptapEditor\Data\MentionItem;
use Illuminate\Contracts\Support\Arrayable;

trait HasMentions
{
    protected array | Closure | null $mentionItems = null;

    protected string | Closure | null $emptyMentionItemsMessage = null;

    protected string | Closure | null $mentionItemsPlaceholder = null;

    protected int | Closure | null $maxMentionItems = 8;

    protected ?Closure $getMentionItemsUsing = null;

    /**
     * Set mention suggestions.
     *
     * @param  array|Closure|null  $suggestions  Either a hardcoded array or an array with MentionItem objects
     */
    public function mentionItems(array | Closure | null $suggestions): static
    {
        $this->mentionItems = $suggestions;

        return $this;
    }

    public function getMentionItems(): ?array
    {
        $items = $this->evaluate($this->mentionItems);
        if (is_null($items)) {
            return null;
        }

        return collect($items)
            ->map(fn ($item) => $item instanceof MentionItem ? $item->toArray() : $item)
            ->toArray();
    }

    /**
     * Set the message to display when no mention suggestions are found.
     */
    public function emptyMentionItemsMessage(string | Closure | null $message): static
    {
        $this->emptyMentionItemsMessage = $message;

        return $this;
    }

    public function getEmptyMentionItemsMessage(): string
    {
        return $this->evaluate($this->emptyMentionItemsMessage) ?? trans('filament-tiptap-editor::editor.mentions.no_suggestions_found');
    }

    /**
     * Show a maximum of mention items
     */
    public function maxMentionItems(int | Closure | null $maxItems): static
    {
        $this->maxMentionItems = $maxItems;

        return $this;
    }

    public function getMaxMentionItems(): ?int
    {
        return $this->evaluate($this->maxMentionItems);
    }

    /**
     * Set the message to display in the empty suggestions state when the trigger character is typed but no input is provided.
     */
    public function mentionItemsPlaceholder(string | Closure | null $message): static
    {
        $this->mentionItemsPlaceholder = $message;

        return $this;
    }

    public function getMentionItemsPlaceholder(): ?string
    {
        return $this->evaluate($this->mentionItemsPlaceholder);
    }

    public function getMentionItemsUsing(?Closure $callback): static
    {
        $this->getMentionItemsUsing = $callback;

        return $this;
    }

    public function getMentionItemsUsingEnabled(): bool
    {
        return ! is_null($this->getMentionItemsUsing);
    }

    public function getSearchResults(string $search): array
    {
        if (! $this->getMentionItemsUsing) {
            return [];
        }

        $results = $this->evaluate($this->getMentionItemsUsing, [
            'query' => $search,
        ]);

        if ($results instanceof Arrayable) {
            $results = $results->toArray();
        }

        return $results;
    }
}
