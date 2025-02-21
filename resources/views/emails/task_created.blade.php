<!DOCTYPE html>
<html>
<head>
    <title>Task Created</title>
</head>
<body>
    <h2>Hello, {{ $task->user->name ?? 'User' }}</h2>
    <p>A new task has been created:</p>
    <p><strong>Title:</strong> {{ $task->title ?? 'No title provided' }}</p>
    <p><strong>Description:</strong> {{ $task->description ?? 'No description provided' }}</p>
    <p><strong>Due Date:</strong> {{ $task->due_date ?? 'No due date provided' }}</p>
    <p><strong>Status:</strong> {{ $task->status ?? 'No status provided' }}</p>
    <p><strong>Priority:</strong> {{ $task->priority ?? 'No priority set' }}</p>
    <p>Thank you !</p>
</body>
</html>
