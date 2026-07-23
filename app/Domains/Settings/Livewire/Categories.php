<?php

namespace App\Domains\Settings\Livewire;

use App\Domains\Expenses\Models\ExpenseCategory;
use App\Shared\Livewire\WithToast;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination, WithToast;

    public ?int $categoryId = null;
    public $parent_id = '';
    public string $name_ar = '';
    public string $name_fr = '';
    public string $name_en = '';
    public string $key = '';
    public string $icon = 'folder';
    public bool $is_active = true;

    public bool $showForm = false;

    public function mount(): void
    {
        Gate::authorize('manage-categories');
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:expense_categories,id',
            'name_ar' => 'required|string|max:255',
            'name_fr' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'key' => 'required|string|max:255|unique:expense_categories,key,' . $this->categoryId,
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->parent_id = $category->parent_id ?? '';
        $this->name_ar = $category->name_ar;
        $this->name_fr = $category->name_fr ?? '';
        $this->name_en = $category->name_en ?? '';
        $this->key = $category->key;
        $this->icon = $category->icon ?? 'folder';
        $this->is_active = $category->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        Gate::authorize('manage-categories');

        if ($this->parent_id === '') {
            $this->parent_id = null;
        }

        if (empty($this->key)) {
            $this->key = Str::slug($this->name_en ?: $this->name_fr ?: $this->name_ar);
        }

        $this->validate();

        $data = [
            'parent_id' => $this->parent_id,
            'name_ar' => $this->name_ar,
            'name_fr' => $this->name_fr,
            'name_en' => $this->name_en,
            'key' => $this->key,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
        ];

        if ($this->categoryId) {
            ExpenseCategory::findOrFail($this->categoryId)->update($data);
            $this->notify(__('common.updated'));
        } else {
            ExpenseCategory::create($data);
            $this->notify(__('common.created'));
        }

        $this->resetForm();
    }

    public function delete(int $id)
    {
        Gate::authorize('manage-categories');

        $category = ExpenseCategory::withCount(['expenses', 'children'])->findOrFail($id);
        
        if ($category->expenses_count > 0) {
            return;
        }

        if ($category->children_count > 0) {
            return;
        }

        $category->delete();
        $this->notify(__('common.deleted'));
    }

    public function toggleActive(int $id)
    {
        Gate::authorize('manage-categories');

        $category = ExpenseCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        $this->notify(__('common.saved'));
    }

    public function resetForm()
    {
        $this->reset(['categoryId', 'parent_id', 'name_ar', 'name_fr', 'name_en', 'key', 'icon', 'is_active']);
        $this->is_active = true;
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        $categories = ExpenseCategory::withCount(['expenses', 'children'])->with('parent')->latest()->paginate(15);

        $parentCategories = ExpenseCategory::whereNull('parent_id')->where('is_active', true)->get();

        return view('livewire.categories', compact('categories', 'parentCategories'))
            ->layout('layouts.app')
            ->title(__('settings.categories') ?? 'Expense Categories');
    }
}
