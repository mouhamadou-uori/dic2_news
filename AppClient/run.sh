#!/bin/bash

echo "🚀 Compilation et lancement de l'application JavaFX..."

# Compilation du projet
echo "📦 Compilation avec Maven..."
mvn clean compile

# Lancement de l'application
echo "▶️ Lancement de l'application..."
mvn javafx:run
