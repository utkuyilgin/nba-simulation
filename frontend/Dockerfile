# Base image
FROM node:16

# Set working directory
WORKDIR /app

# Install dependencies
COPY package*.json ./
RUN npm install

# Copy the rest of the application
COPY . .

# Expose port
EXPOSE 8020

# Run the app in dev mode
CMD ["npm", "run", "serve"]
