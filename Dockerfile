# Use a minimal NGINX image to serve the static restaurant website
FROM nginx:stable-alpine

# Copy project files into the default nginx html directory
COPY . /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
