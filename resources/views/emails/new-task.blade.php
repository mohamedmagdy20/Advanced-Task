<!DOCTYPE html>
<html>

<head>
    <title>New Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .task-details {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Task</h1>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <p>This is a friendly reminder that you have a new task add to your to do list:</p>

            <div class="task-details">
                <h3>{{ $task->title }}</h3>
                <p><strong>Description:</strong> {{ $task->description ?? 'No description provided' }}</p>
                <p><strong>Due Date:</strong> {{ $task->due_date }}</p>
                <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
            </div>

            <p>Please make sure to complete this task on time. If you have any questions or need assistance, please
                don't hesitate to reach out.</p>
        </div>

        <div class="footer">
            <p>This is an automated reminder. Please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>