
---

# Application Features

### Authentication
  Users can register and log in using JWT tokens. Protected routes ensure that only authenticated users access sensitive endpoints.
### Task Management
  Create, read, update, and delete tasks.
  Users can add a task "priority" if they have a paid subscription
### CI/CD Pipeline
  Automated continuous integration and continuous deployment with github actions.
### Asynchronous Operations
  Background tasks such as sending email notifications when a new task is created are handled asynchronously with Laravel Queues.
### Feature Testing
  Test authentication and task CRUD operations to ensure feature reliability.
### Payment Gateway Simulation 
 Stripe Integration (Simulated) : A mock payment form allows users to "unlock" premium features.
### Code Quality & Security
  The application follows best practices to ensure a secure, maintainable, and high-quality codebase.

---

# API Documentation

### Authentication
The application uses JWT (JSON Web Tokens) for authentication. Users must register and log in to receive a token. Include the token in the `Authorization` header as a Bearer token for all protected routes.

---

### API Endpoints Overview
#### Base URL
https://lll-pi-ten.vercel.app/api/api

| **Endpoint**               | **Method** | **Auth Required** | **Description**                                               |
|----------------------------|------------|-------------------|---------------------------------------------------------------|
| **User Authentication**    |            |                   |                                                               |
| `/auth/register`           | POST       | No                | Registers a new user.                                         |
| `/auth/login`              | POST       | No                | Logs in an existing user.                                     |
| `/auth/me`                 | GET        | Yes               | Retrieves the authenticated user's details.                 |
| `/auth/logout`             | POST       | Yes               | Logs out the authenticated user.                              |
| `/auth/refresh`            | POST       | Yes               | Refreshes the JWT token.                                      |
| **Task Management**        |            |                   |                                                               |
| `/tasks`                   | GET        | Yes               | Fetches all tasks for the authenticated user.                 |
| `/tasks`                   | POST       | Yes               | Creates a new task. *If a `priority` field is provided, the user must be paid.* |
| `/tasks/{task}`            | PUT        | Yes               | Updates an existing task. *Updating `priority` requires a paid subscription.* |
| `/tasks/{task}`            | DELETE     | Yes               | Deletes a task.                                               |
| **Payment**                   |            |                   |                                                               |
| `/create-payment-intent`   | GET        | Yes               | Creates a payment intent for processing payments.             |

---

### Detailed Endpoint Explanations

#### User Authentication

1. **Register a User**
   - **Endpoint:** `/auth/register`
   - **Method:** `POST`
   - **Request Body:**
     ```json
     {
         "name": "string",
         "email": "string (valid email)",
         "password": "string (min: 8 characters)",
         "password_confirmation": "string (must match password)"
     }
     ```
   - **Response:**
     - **Success (201):** Returns a success message, user details, and a JWT token.
     - **Error (400):** Returns validation error messages.

2. **Login a User**
   - **Endpoint:** `/auth/login`
   - **Method:** `POST`
   - **Request Body:**
     ```json
     {
         "email": "string (valid email)",
         "password": "string (min: 8 characters)"
     }
     ```
   - **Response:**
     - **Success (200):** Returns a JWT token, token type, and expiry duration.
     - **Error (401):** Returns an error message for invalid credentials.

3. **Fetch Authenticated User**
   - **Endpoint:** `/auth/me`
   - **Method:** `GET`
   - **Headers:** `Authorization: Bearer {token}`
   - **Response:**
     - **Success (200):** Returns the authenticated user's information.



#### Task Management

1. **Fetch User Tasks**
   - **Endpoint:** `/tasks`
   - **Method:** `GET`
   - **Headers:** `Authorization: Bearer {token}`
   - **Response:**
     - **Success (200):** Returns an array of task objects containing properties like `id`, `title`, `description`, `status`, `due_date`, and `priority`.

2. **Create a Task**
   - **Endpoint:** `/tasks`
   - **Method:** `POST`
   - **Headers:** `Authorization: Bearer {token}`
   - **Request Body:**
     ```json
     {
         "title": "string (required)",
         "description": "string (optional)",
         "status": "string (required, values: pending, completed)",
         "due_date": "date (optional, must be today or later)",
         "priority": "string (optional, values: low, medium, high)"
     }
     ```
   - **Important Note:**  
     If the request includes a `priority` field, the API verifies if the user is paid (using the `isPaid()` method). If not, it returns a 403 error with the message:  
     `"Priority feature is available for paid users only"`.
   - **Response:**
     - **Success (201):** Returns the newly created task object.
     - **Error (403):** Returns an error if the user is not paid and attempts to use the priority feature.

3. **Update a Task**
   - **Endpoint:** `/tasks/{task}`
   - **Method:** `PUT`
   - **Headers:** `Authorization: Bearer {token}`
   - **Request Body:** (Include only fields to update)
     ```json
     {
         "title": "string (optional)",
         "description": "string (optional)",
         "status": "string (optional, values: pending, completed)",
         "due_date": "date (optional, must be today or later)",
         "priority": "string (optional, values: low, medium, high)"
     }
     ```
   - **Important Note:**  
     If the update includes the `priority` field, the endpoint checks if the authenticated user is paid. If not, it returns a 403 error with the message:  
     `"Priority feature is available for paid users only"`.
   - **Response:**
     - **Success (200):** Returns the updated task object.
     - **Error (403):** Returns an error if the user is not paid but attempts to update the priority field.

4. **Delete a Task**
   - **Endpoint:** `/tasks/{task}`
   - **Method:** `DELETE`
   - **Headers:** `Authorization: Bearer {token}`
   - **Response:**
     - **Success (200):** Returns a message confirming the deletion of the task.

#### Payment

1. **Payment Intent Creation**
   - **Endpoint:** `/create-payment-intent`
   - **Method:** `GET`
   - **Headers:** `Authorization: Bearer {token}`
   - **Response:**
     - **Success (200):** Returns a `clientSecret` and `paymentIntentId` for payment processing.
     - **Error (400):** Returns an error message if the user has already paid.

---

### Error Handling
All endpoints return appropriate HTTP status codes along with JSON-formatted error responses, covering validation errors, unauthorized access, and other potential issues.

---
