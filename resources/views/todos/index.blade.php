<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Todo List</h1>
        <div class="input-group mb-3">
            <input type="text" id="task" class="form-control" placeholder="Enter task">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="add-task">Add Task</button>
            </div>
        </div>
        <button class="btn btn-secondary mb-3" id="show-tasks">Show All Tasks</button>
        <ul class="list-group" id="todo-list"></ul>
    </div>

    <script>
        $(document).ready(function() {
            $('#add-task').click(function() {
                const task = $('#task').val();
                if (task === '') {
                    alert('Task cannot be empty');
                    return;
                }

                $.ajax({
                    url: '/todo',
                    method: 'POST',
                    data: {
                        task: task,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        $('#task').val('');
                        loadTasks();
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            $('#show-tasks').click(function() {
                loadTasks();
            });

            function loadTasks() {
                $.ajax({
                    url: '/todos',
                    method: 'GET',
                    success: function(data) {
                        $('#todo-list').empty();
                        data.forEach(function(todo) {
                            $('#todo-list').append(`
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ${todo.task}
                                    <div>
                                        <input type="checkbox" ${todo.completed ? 'checked' : ''} data-id="${todo.id}" class="toggle-completed">
                                        <button class="btn btn-danger btn-sm delete-task" data-id="${todo.id}">Delete</button>
                                    </div>
                                </li>
                            `);
                        });

                        $('.toggle-completed').change(function() {
                            const id = $(this).data('id');
                            $.ajax({
                                url: '/todo/' + id,
                                method: 'PUT',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function() {
                                    loadTasks();
                                }
                            });
                        });

                        $('.delete-task').click(function() {
                            const id = $(this).data('id');
                            if (confirm('Are you sure to delete this task?')) {
                                $.ajax({
                                    url: '/todo/' + id,
                                    method: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function() {
                                        loadTasks();
                                    }
                                });
                            }
                        });
                    }
                });
            }

            loadTasks();
        });
    </script>
</body>
</html>
