<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;

    public $search;

    public $editingTodoID;

    #[Rule('required|min:3|max:50')]
    public $editingTodoName;

    public function create()
    {
        $validated = $this->validateOnly('name');

        Todo::query()
            ->create($validated);

        $this->reset('name');

        session()->flash('success', 'Created.');
    }

    public function toggle($todoID)
    {
        $todo = Todo::query()->find($todoID);
        $todo->update([
            'completed' => !$todo->completed,
        ]);
    }

    public function edit($todoID)
    {
        $this->editingTodoID = $todoID;
        $this->editingTodoName = Todo::find($todoID)->name;
    }

    public function update()
    {
        $this->validateOnly('editingTodoName');

        Todo::find($this->editingTodoID)->update([
            'name' => $this->editingTodoName,
        ]);

        $this->cancelEdit();
    }

    public function delete($todoID)
    {
        Todo::query()
            ->findOrFail($todoID)->delete();
    }

    public function cancelEdit()
    {
        $this->reset('editingTodoID', 'editingTodoName');
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::query()
                ->where('name', 'like', "%{$this->search}%")
                ->paginate(5),
        ]);
    }
}
