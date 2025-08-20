<?php
session_start();

// Initialize todos in session if not exists
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $task = trim($_POST['task'] ?? '');
            if (!empty($task)) {
                $_SESSION['todos'][] = [
                    'id' => uniqid(),
                    'task' => htmlspecialchars($task),
                    'completed' => false,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
            break;

        case 'toggle':
            $id = $_POST['id'] ?? '';
            foreach ($_SESSION['todos'] as &$todo) {
                if ($todo['id'] === $id) {
                    $todo['completed'] = !$todo['completed'];
                    break;
                }
            }
            break;

        case 'delete':
            $id = $_POST['id'] ?? '';
            $_SESSION['todos'] = array_filter($_SESSION['todos'], function($todo) use ($id) {
                return $todo['id'] !== $id;
            });
            break;

        case 'clear_completed':
            $_SESSION['todos'] = array_filter($_SESSION['todos'], function($todo) {
                return !$todo['completed'];
            });
            break;
    }

    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$todos = $_SESSION['todos'];
$totalTodos = count($todos);
$completedTodos = count(array_filter($todos, function($todo) {
    return $todo['completed'];
}));
$pendingTodos = $totalTodos - $completedTodos;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoApp - Modern Task Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-dark: #7c3aed;
            --primary-light: #a78bfa;
            --secondary: #f3f4f6;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --background: #ffffff;
            --surface: #f9fafb;
            --border: #e5e7eb;
            --danger: #ef4444;
            --success: #10b981;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-lg: rgba(0, 0, 0, 0.15);
        }

        [data-theme="dark"] {
            --primary: #a78bfa;
            --primary-dark: #8b5cf6;
            --primary-light: #c4b5fd;
            --secondary: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --background: #111827;
            --surface: #1f2937;
            --border: #374151;
            --danger: #f87171;
            --success: #34d399;
            --shadow: rgba(0, 0, 0, 0.3);
            --shadow-lg: rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .theme-toggle {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .app-card {
            background: var(--background);
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px var(--shadow-lg), 0 10px 10px -5px var(--shadow);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .stat {
            text-align: center;
            padding: 1rem;
            border-radius: 0.5rem;
            background: var(--background);
            border: 1px solid var(--border);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .add-todo {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .add-todo form {
            display: flex;
            gap: 0.75rem;
        }

        .add-todo input {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            background: var(--background);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .add-todo input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .btn {
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--text-secondary);
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .todos-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .todo-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .todo-item:hover {
            background: var(--surface);
        }

        .todo-item:last-child {
            border-bottom: none;
        }

        .todo-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid var(--primary);
            border-radius: 0.25rem;
            cursor: pointer;
            position: relative;
            background: var(--background);
            transition: all 0.3s ease;
        }

        .todo-checkbox.completed {
            background: var(--primary);
        }

        .todo-checkbox.completed::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 0.875rem;
            font-weight: bold;
        }

        .todo-content {
            flex: 1;
        }

        .todo-text {
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .todo-text.completed {
            text-decoration: line-through;
            color: var(--text-secondary);
        }

        .todo-date {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .todo-actions {
            display: flex;
            gap: 0.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .actions-bar {
            padding: 1rem 1.5rem;
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .theme-toggle {
                top: 1rem;
                right: 1rem;
            }

            .add-todo form {
                flex-direction: column;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .todo-item {
                padding: 1rem;
            }
        }

        /* Scrollbar Styling */
        .todos-list::-webkit-scrollbar {
            width: 6px;
        }

        .todos-list::-webkit-scrollbar-track {
            background: var(--surface);
        }

        .todos-list::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        .todos-list::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tasks"></i> TodoApp</h1>
            <p>Stay organized and productive with your modern task manager</p>
        </div>

        <div class="app-card">
            <!-- Statistics -->
            <div class="stats">
                <div class="stat">
                    <div class="stat-number"><?= $totalTodos ?></div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?= $pendingTodos ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?= $completedTodos ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Add Todo -->
            <div class="add-todo">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="task" placeholder="What needs to be done?" required maxlength="255">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Task
                    </button>
                </form>
            </div>

            <!-- Todos List -->
            <div class="todos-list">
                <?php if (empty($todos)): ?>
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>No tasks yet</h3>
                        <p>Add your first task to get started!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($todos as $todo): ?>
                        <div class="todo-item">
                            <form method="POST" style="display: contents;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                                <div class="todo-checkbox <?= $todo['completed'] ? 'completed' : '' ?>"
                                     onclick="this.closest('form').submit()"></div>
                            </form>

                            <div class="todo-content">
                                <div class="todo-text <?= $todo['completed'] ? 'completed' : '' ?>">
                                    <?= $todo['task'] ?>
                                </div>
                                <div class="todo-date">
                                    <i class="fas fa-clock"></i> <?= date('M j, Y g:i A', strtotime($todo['created_at'])) ?>
                                </div>
                            </div>

                            <div class="todo-actions">
                                <form method="POST" style="display: contents;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $todo['id'] ?>">
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this task?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Actions Bar -->
            <?php if (!empty($todos)): ?>
                <div class="actions-bar">
                    <div>
                        <?php if ($completedTodos > 0): ?>
                            <span class="text-secondary">
                                <i class="fas fa-check-circle"></i>
                                <?= $completedTodos ?> task<?= $completedTodos !== 1 ? 's' : '' ?> completed
                            </span>
                        <?php else: ?>
                            <span class="text-secondary">
                                <i class="fas fa-hourglass-half"></i>
                                Keep going! You've got this!
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($completedTodos > 0): ?>
                        <form method="POST" style="display: contents;">
                            <input type="hidden" name="action" value="clear_completed">
                            <button type="submit" class="btn btn-secondary"
                                    onclick="return confirm('Remove all completed tasks?')">
                                <i class="fas fa-broom"></i> Clear Completed
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Theme management
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            const icon = document.getElementById('theme-icon');
            icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            const icon = document.getElementById('theme-icon');
            icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        });

        // Auto-focus on task input
        document.querySelector('input[name="task"]').focus();
    </script>
</body>
</html>