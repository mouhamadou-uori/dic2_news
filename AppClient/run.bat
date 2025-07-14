@echo off
echo 🚀 Compilation et lancement de l'application JavaFX...

REM Compilation du projet
echo 📦 Compilation avec Maven...
mvn clean compile

REM Lancement de l'application
echo ▶️ Lancement de l'application...
mvn javafx:run

pause
