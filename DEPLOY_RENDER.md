# Deploying this project to Render (manual steps)

This repository includes a Dockerfile so you can deploy easily to any container-based platform (Render, Railway, Fly, DigitalOcean App Platform, etc.). Below are step-by-step instructions for Render (manual, one-time setup):

1) Create a Render account
- Sign up at https://render.com and connect your GitHub account.

2) Create a new "Web Service"
- Click "New" → "Web Service".
- Select this repository: Fahim232/job_placement_grooming.
- For Environment choose: "Docker" (the repo contains a Dockerfile).
- Branch: add/docker-compose (or main if you merge the branch).

3) Add environment variables
- In Render's service settings, add the following environment variables (or use the Render Managed Database connection info):
  - MYSQL_ROOT_PASSWORD (set a strong value)
  - MYSQL_USER (e.g., appuser)
  - MYSQL_PASSWORD (set a strong value)
  - MYSQL_DATABASE (projects)
  - DB_HOST (if you use a managed DB provided by Render, set DB_HOST to the host Render provides — otherwise use an external MySQL host)
  - DB_USER (appuser)
  - DB_PASS (app_password)
  - DB_NAME (projects)

4) Database initialization
- If you use Render Managed Databases (MySQL support may vary), configure the database from Render and run the SQL from database.sql to create tables and seed data.
- Alternatively, use an external managed MySQL (ClearDB, PlanetScale, etc.) and paste the SQL via their UI or mysql client.

5) Deploy
- Click "Create Web Service" and Render will build the Docker image and deploy.
- After deployment Render will provide a public URL like https://your-app.onrender.com where you can open the site in a browser.

Notes
- The Dockerfile installs mysqli and pdo_mysql extensions used by the app.
- The repository includes database.sql which must be imported into the database used by the running app. The app relies on an external MySQL database — the Dockerfile does not run MySQL in the same container.
- For quick local testing use docker compose (we added docker-compose.yml in this branch). For local dev the Compose stack provides MySQL + phpMyAdmin + web server.

Troubleshooting
- If the app cannot connect to DB on Render, double-check DB_HOST, DB_USER, DB_PASS, DB_NAME and that the DB allows connections from Render.
- If SQL didn't import, run the SQL manually against your managed DB.

If you want, I can also add a Render template file (render.yaml) to automate creation of a service template — confirm and I will add it.