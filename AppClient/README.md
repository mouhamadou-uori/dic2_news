# Application de Gestion des Utilisateurs - JavaFX

## 📋 Description

Application JavaFX moderne et futuriste pour la gestion des utilisateurs dans le cadre du projet d'architecture logicielle. L'interface offre une expérience utilisateur intuitive avec un design contemporain.

## ✨ Fonctionnalités

### 🔐 Authentification
- Interface de connexion élégante avec animations
- Validation des identifiants administrateur
- Compte de test : `admin` / `admin123`

### 👥 Gestion des Utilisateurs
- **Affichage** : Liste complète avec recherche en temps réel
- **Ajout** : Création de nouveaux utilisateurs avec validation
- **Modification** : Édition des informations utilisateur
- **Suppression** : Suppression avec confirmation
- **Statistiques** : Compteurs d'utilisateurs total et actifs

### 🎨 Interface Moderne
- Design futuriste avec palette de couleurs moderne
- Animations fluides et transitions élégantes
- Icônes Material Design
- Interface responsive et intuitive
- Thème sombre/clair adaptatif

## 🛠️ Technologies Utilisées

- **JavaFX 19** - Interface utilisateur
- **Maven** - Gestion des dépendances
- **Ikonli** - Icônes Material Design
- **Jackson** - Traitement JSON
- **CSS3** - Styles modernes

## 📦 Structure du Projet

\`\`\`
src/
├── main/
│   ├── java/
│   │   └── com/newsapp/client/
│   │       ├── UserManagementApp.java      # Classe principale
│   │       ├── controller/                 # Contrôleurs FXML
│   │       ├── model/                      # Modèles de données
│   │       └── service/                    # Services métier
│   └── resources/
│       ├── fxml/                          # Fichiers FXML
│       └── css/                           # Feuilles de style
\`\`\`

## 🚀 Installation et Exécution

### Prérequis
- Java 17 ou supérieur
- Maven 3.6+
- JavaFX SDK (inclus dans les dépendances)

### Commandes d'exécution

#### Windows
\`\`\`bash
# Méthode 1 : Script batch
run.bat

# Méthode 2 : Commandes Maven
mvn clean compile
mvn javafx:run
\`\`\`

#### Linux/Mac
\`\`\`bash
# Méthode 1 : Script shell
chmod +x run.sh
./run.sh

# Méthode 2 : Commandes Maven
mvn clean compile
mvn javafx:run
\`\`\`

#### Alternative avec JAR
\`\`\`bash
# Créer un JAR exécutable
mvn clean package

# Exécuter le JAR (nécessite JavaFX dans le classpath)
java --module-path /path/to/javafx/lib --add-modules javafx.controls,javafx.fxml -jar target/user-management-client-1.0.0.jar
\`\`\`

## 🎯 Utilisation

### Connexion
1. Lancez l'application
2. Utilisez les identifiants : `admin` / `admin123`
3. Cliquez sur "Se connecter"

### Gestion des Utilisateurs
1. **Recherche** : Utilisez la barre de recherche pour filtrer
2. **Ajout** : Cliquez sur "Ajouter" et remplissez le formulaire
3. **Modification** : Sélectionnez un utilisateur et cliquez sur "Modifier"
4. **Suppression** : Sélectionnez un utilisateur et cliquez sur "Supprimer"
5. **Actualisation** : Cliquez sur "Actualiser" pour recharger les données

### Rôles Disponibles
- **Administrateur** : Accès complet à toutes les fonctionnalités
- **Éditeur** : Gestion du contenu
- **Visiteur** : Consultation uniquement

## 🎨 Personnalisation

### Thèmes et Couleurs
Les couleurs sont définies dans `src/main/resources/css/styles.css` :
- Primaire : #2196F3 (Bleu)
- Secondaire : #607D8B (Gris-bleu)
- Succès : #4CAF50 (Vert)
- Danger : #F44336 (Rouge)

### Ajout de Fonctionnalités
1. Modifiez les modèles dans `model/`
2. Ajoutez la logique dans `service/`
3. Mettez à jour les contrôleurs dans `controller/`
4. Adaptez les vues FXML dans `resources/fxml/`

## 🔧 Configuration

### Variables d'Environnement
Aucune configuration spéciale requise pour la version statique.

### Base de Données
Version actuelle : Données simulées en mémoire
Version future : Intégration avec services SOAP/REST

## 📝 Notes de Développement

- **Architecture MVC** : Séparation claire des responsabilités
- **Validation** : Validation côté client avec feedback visuel
- **Animations** : Transitions fluides pour une meilleure UX
- **Responsive** : Interface adaptative selon la taille d'écran
- **Accessibilité** : Support des raccourcis clavier

## 🐛 Dépannage

### Problèmes Courants

1. **JavaFX non trouvé**
   \`\`\`bash
   # Vérifiez la version Java
   java --version
   
   # Utilisez le plugin Maven JavaFX
   mvn javafx:run
   \`\`\`

2. **Erreur de compilation**
   \`\`\`bash
   # Nettoyez et recompilez
   mvn clean compile
   \`\`\`

3. **Interface ne s'affiche pas**
   - Vérifiez que les fichiers FXML sont dans `src/main/resources/fxml/`
   - Vérifiez les chemins dans les contrôleurs

## 📄 Licence

Projet académique - Université [Nom de l'université]

## 👥 Équipe de Développement

- **Groupe X** - [Noms des étudiants]
- **Classe** : [DIC2/MASTER1/DIT2]
- **Matière** : Architecture Logicielle

---

*Application développée dans le cadre du projet d'architecture logicielle*
