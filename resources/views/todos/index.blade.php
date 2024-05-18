
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-HJbde+zJjw8N+DmYAkFvjGmxZv6qrotDqU/UrsPzPn2hvXy8nR4e9yoWY+smudMK" crossorigin="anonymous">

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
            // Function to load tasks
            function loadTasks() {
                $.ajax({
                    url: '/todos', // Replace with your API endpoint for fetching tasks
                    method: 'GET',
                    success: function(data) {
                        $('#todo-list').empty();
                        data.forEach(function(todo) {
                            let completedClass = todo.completed ? 'completed' : '';
                            $('#todo-list').append(`
                                <li class="list-group-item d-flex justify-content-between align-items-center ${completedClass}" data-id="${todo.id}">
                                    ${todo.task}
                                    <div>
                                        <input type="checkbox" ${todo.completed ? 'checked' : ''} class="toggle-completed">
                                        <button class="btn btn-danger btn-sm delete-task">Delete</button>
                                    </div>
                                </li>
                            `);
                        });
                    }
                });
            }

            // Load uncompleted tasks on page load
            function loadUncompletedTasks() {
                $.ajax({
                    url: '/todos', // Replace with your API endpoint for fetching tasks
                    method: 'GET',
                    success: function(data) {
                        $('#todo-list').empty();
                        data.forEach(function(todo) {
                            if (!todo.completed) {
                                $('#todo-list').append(`
                                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${todo.id}">
                                        ${todo.task}
                                        <div>
                                            <input type="checkbox" ${todo.completed ? 'checked' : ''} class="toggle-completed">
                                            <button class="btn btn-danger btn-sm delete-task">Delete</button>
                                        </div>
                                    </li>
                                `);
                            }
                        });
                    }
                });
            }

            // Load uncompleted tasks on page load
            loadUncompletedTasks();

            // Add task
            $('#add-task').click(function() {
                const task = $('#task').val();
                if (task === '') {
                    alert('Task cannot be empty');
                    return;
                }

                $.ajax({
                    url: '/todo', // Replace with your API endpoint for adding tasks
                    method: 'POST',
                    data: {
                        task: task,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function() {
                        $('#task').val('');
                        loadUncompletedTasks(); // Load uncompleted tasks after adding a new task
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });

            // Toggle completed
            $(document).on('change', '.toggle-completed', function() {
                const id = $(this).closest('li').data('id');
                $.ajax({
                    url: '/todo/' + id, // Replace with your API endpoint for updating task status
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function() {
                        loadUncompletedTasks(); // Load uncompleted tasks after updating task status
                    }
                });
            });

            // Delete task
            $(document).on('click', '.delete-task', function() {
                const id = $(this).closest('li').data('id');
                if (confirm('Are you sure to delete this task?')) {
                    $.ajax({
                        url: '/todo/' + id, // Replace with your API endpoint for deleting tasks
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function() {
                            loadUncompletedTasks(); // Load uncompleted tasks after deleting a task
                        }
                    });
                }
            });

            // Show all tasks (both completed and uncompleted)
            $('#show-tasks').click(function() {
                const showAll = $(this).hasClass('active');
                if (!showAll) {
                    $(this).addClass('active');
                    loadTasks(); // Load all tasks when "Show All Tasks" button is clicked
                } else {
                    $(this).removeClass('active');
                    loadUncompletedTasks(); // Load uncompleted tasks when "Show All Tasks" button is clicked again
                }
            });
        });
    </script>

</body>
</html>
