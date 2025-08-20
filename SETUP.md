# TodoApp - Modern PHP Todo List Application

## 📁 Project Structure

```
todoapp/
├── index.php              # Main application file
├── config/
│   └── app.php            # Application configuration
├── includes/
│   ├── functions.php      # Helper functions
│   └── session.php        # Session management
├── assets/
│   ├── css/
│   │   └── custom.css     # Additional custom styles
│   ├── js/
│   │   └── app.js         # Additional JavaScript
│   └── images/
│       └── favicon.ico    # Application favicon
├── .htaccess              # Apache configuration
├── README.md              # Project documentation
└── .gitignore             # Git ignore file
```

## 🚀 Step-by-Step Setup Guide

### Prerequisites
- **PHP 7.4 or higher** (with session support)
- **Web server** (Apache, Nginx, or PHP built-in server)
- **Modern web browser**

### Step 1: Download and Extract
1. Create a new folder called `todoapp` on your computer
2. Save the `index.php` file from above into this folder

### Step 2: Create Additional Files (Optional Enhancement)

#### Create `.htaccess` file:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

#### Create `README.md`:
```markdown
# TodoApp

A modern, responsive todo list application built with native PHP.

## Features
- ✨ Modern, responsive design
- 🌙 Dark/Light mode toggle
- 💜 Beautiful purple theme
- 📱 Mobile-friendly interface
- 🔄 Session-based storage
- ⚡ Fast and lightweight

## Quick Start
1. Ensure PHP 7.4+ is installed
2. Run: `php -S localhost:8000`
3. Open: http://localhost:8000
```

### Step 3: Choose Your Running Method

#### Option A: PHP Built-in Server (Recommended for Development)
1. Open terminal/command prompt
2. Navigate to your project folder:
   ```bash
   cd path/to/todoapp
   ```
3. Start the PHP server:
   ```bash
   php -S localhost:8000
   ```
4. Open your browser and go to: `http://localhost:8000`

#### Option B: XAMPP/WAMP/MAMP
1. Install XAMPP, WAMP, or MAMP
2. Copy the `todoapp` folder to:
    - **XAMPP**: `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
    - **WAMP**: `C:\wamp64\www\`
    - **MAMP**: `/Applications/MAMP/htdocs/`
3. Start Apache from your control panel
4. Open browser and go to: `http://localhost/todoapp`

#### Option C: Docker (Advanced)
Create a `Dockerfile`:
```dockerfile
FROM php:8.1-apache
COPY . /var/www/html/
EXPOSE 80
```

Then run:
```bash
docker build -t todoapp .
docker run -p 8080:80 todoapp
```

### Step 4: Test the Application
1. Open the application in your browser
2. Try adding a new task
3. Mark tasks as complete/incomplete
4. Test the delete functionality
5. Toggle between dark and light modes
6. Test on mobile devices

## 🎨 Features Overview

### Core Functionality
- **Add Tasks**: Enter tasks and press "Add Task"
- **Mark Complete**: Click the checkbox to toggle completion
- **Delete Tasks**: Click the trash icon to remove tasks
- **Clear Completed**: Remove all completed tasks at once
- **Theme Toggle**: Switch between light and dark modes

### UI Features
- **Responsive Design**: Works perfectly on desktop and mobile
- **Modern Styling**: Clean, contemporary interface
- **Purple Theme**: Beautiful purple color scheme
- **Statistics**: Shows total, pending, and completed tasks
- **Timestamps**: Each task shows when it was created
- **Smooth Animations**: Hover effects and transitions

### Technical Features
- **Session Storage**: Tasks persist during browser session
- **Form Validation**: Client and server-side validation
- **Security**: XSS protection and input sanitization
- **Accessibility**: Keyboard navigation and screen reader support
- **Performance**: Optimized CSS and minimal JavaScript

## 🛠️ Customization

### Changing Colors
Edit the CSS variables in `index.php`:
```css
:root {
    --primary: #your-color-here;
    --primary-dark: #your-darker-color;
    /* ... other variables */
}
```

### Adding Features
The application is built with a simple structure that makes it easy to extend:
- Add new actions in the POST handler
- Create new UI components in the HTML
- Extend the todo array structure for more fields

## 🔧 Troubleshooting

### Common Issues

1. **"Session not working"**
    - Ensure PHP has session support enabled
    - Check that the web server can write to the session directory

2. **"Styles not loading"**
    - Ensure you're accessing via HTTP (not file://)
    - Check browser console for any errors

3. **"Form submissions not working"**
    - Verify PHP is processing POST requests
    - Check that the web server is configured correctly

### Browser Compatibility
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## 📝 License
This project is open source and available under the MIT License.

## 🤝 Contributing
Feel free to fork this project and submit pull requests for any improvements!