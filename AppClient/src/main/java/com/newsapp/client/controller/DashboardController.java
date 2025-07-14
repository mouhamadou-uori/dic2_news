package com.newsapp.client.controller;

import com.newsapp.client.model.User;
import com.newsapp.client.service.UserService;
import javafx.animation.FadeTransition;
import javafx.collections.transformation.FilteredList;
import javafx.collections.transformation.SortedList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.stage.Modality;
import javafx.stage.Stage;
import javafx.util.Duration;
import org.kordamp.ikonli.javafx.FontIcon;
import org.kordamp.ikonli.materialdesign2.*;

import java.io.IOException;
import java.net.URL;
import java.util.Optional;
import java.util.ResourceBundle;

public class DashboardController implements Initializable {
    @FXML private VBox dashboardContainer;
    @FXML private TableView<User> usersTable;
    @FXML private TableColumn<User, String> userColumn;
    @FXML private TableColumn<User, String> statusColumn;
    @FXML private TableColumn<User, String> roleColumn;
    @FXML private TableColumn<User, String> actionsColumn;
    @FXML private Label totalUsersLabel;
    @FXML private Label activeSessionsLabel;
    @FXML private Label roleDistributionLabel;
    @FXML private TextField searchField;

    private UserService userService;
    private FilteredList<User> filteredUsers;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        userService = UserService.getInstance();
        setupTable();
        setupSearch();
        setupUI();
        setupEventHandlers();
        loadUsers();
        playEntryAnimation();
    }

    private void setupTable() {
        usersTable.setColumnResizePolicy(TableView.CONSTRAINED_RESIZE_POLICY);
        
        userColumn.setCellValueFactory(new PropertyValueFactory<>("username"));
        statusColumn.setCellValueFactory(new PropertyValueFactory<>("status"));
        roleColumn.setCellValueFactory(new PropertyValueFactory<>("role"));
        actionsColumn.setCellValueFactory(new PropertyValueFactory<>("actions"));

        statusColumn.setCellFactory(column -> new TableCell<User, String>() {
            @Override
            protected void updateItem(String status, boolean empty) {
                super.updateItem(status, empty);
                if (empty || status == null) {
                    setText(null);
                    setGraphic(null);
                    setStyle("");
                } else {
                    setText(status);
                    if ("Actif".equals(status)) {
                        setStyle("-fx-text-fill: #10b981; -fx-font-weight: bold;");
                    } else if ("Inactif".equals(status)) {
                        setStyle("-fx-text-fill: #ef4444; -fx-font-weight: bold;");
                    } else if ("En pause".equals(status)) {
                        setStyle("-fx-text-fill: #f59e0b; -fx-font-weight: bold;");
                    } else {
                        setStyle("-fx-text-fill: #9ca3af; -fx-font-weight: bold;");
                    }
                }
            }
        });

        roleColumn.setCellFactory(column -> new TableCell<User, String>() {
            @Override
            protected void updateItem(String role, boolean empty) {
                super.updateItem(role, empty);
                if (empty || role == null) {
                    setText(null);
                    setStyle("");
                } else {
                    setText(role);
                    setStyle("-fx-text-fill: #ffffff; -fx-font-weight: normal;");
                }
            }
        });

        actionsColumn.setCellFactory(col -> new TableCell<User, String>() {
            private final HBox actionBox = new HBox(8);
            private final Button viewBtn = new Button();
            private final Button editBtn = new Button();
            private final Button deleteBtn = new Button();

            {
                viewBtn.getStyleClass().add("dashboard-action-btn");
                editBtn.getStyleClass().add("dashboard-action-btn");
                deleteBtn.getStyleClass().addAll("dashboard-action-btn", "delete");

                viewBtn.setGraphic(new FontIcon(MaterialDesignE.EYE));
                editBtn.setGraphic(new FontIcon(MaterialDesignP.PENCIL));
                deleteBtn.setGraphic(new FontIcon(MaterialDesignD.DELETE));

                viewBtn.setOnAction(e -> handleViewUser(getTableView().getItems().get(getIndex())));
                editBtn.setOnAction(e -> handleEditUser(getTableView().getItems().get(getIndex())));
                deleteBtn.setOnAction(e -> handleDeleteUser(getTableView().getItems().get(getIndex())));

                actionBox.getChildren().addAll(viewBtn, editBtn, deleteBtn);
                actionBox.setAlignment(javafx.geometry.Pos.CENTER_LEFT);
            }

            @Override
            protected void updateItem(String item, boolean empty) {
                super.updateItem(item, empty);
                if (empty) {
                    setGraphic(null);
                } else {
                    setGraphic(actionBox);
                }
                setText(null);
            }
        });
    }

    private void setupSearch() {
        filteredUsers = new FilteredList<>(userService.getAllUsers(), p -> true);
        
        searchField.textProperty().addListener((observable, oldValue, newValue) -> {
            filteredUsers.setPredicate(user -> {
                if (newValue == null || newValue.isEmpty()) {
                    return true;
                }
                
                String lowerCaseFilter = newValue.toLowerCase();
                
                if (user.getUsername().toLowerCase().contains(lowerCaseFilter)) {
                    return true;
                } else if (user.getRole().toLowerCase().contains(lowerCaseFilter)) {
                    return true;
                } else if (user.getStatus().toLowerCase().contains(lowerCaseFilter)) {
                    return true;
                }
                return false;
            });
        });
        
        SortedList<User> sortedUsers = new SortedList<>(filteredUsers);
        sortedUsers.comparatorProperty().bind(usersTable.comparatorProperty());
        usersTable.setItems(sortedUsers);
    }

    private void setupUI() {
    }

    private void setupEventHandlers() {
    }

    private void loadUsers() {
        updateStatistics();
    }

    private void updateStatistics() {
        int total = userService.getAllUsers().size();
        int active = (int) userService.getAllUsers().stream()
                .filter(user -> "Actif".equals(user.getStatus())).count();
        int roles = (int) userService.getAllUsers().stream()
                .map(User::getRole).distinct().count();

        totalUsersLabel.setText(String.valueOf(total));
        activeSessionsLabel.setText(String.valueOf(active));
        roleDistributionLabel.setText(String.valueOf(roles));
    }

    private void playEntryAnimation() {
        FadeTransition fadeIn = new FadeTransition(Duration.millis(600), dashboardContainer);
        fadeIn.setFromValue(0.0);
        fadeIn.setToValue(1.0);
        fadeIn.play();
    }

    @FXML
    private void handleAddUser() {
        openUserDialog(null);
    }

    @FXML
    private void handleRefresh() {
        loadUsers();
        showSuccessMessage("Liste des utilisateurs actualisée");
    }

    private boolean showCustomConfirmDialog(String title, String message) {
        final boolean[] result = {false};
        Stage dialog = new Stage();
        dialog.initModality(Modality.APPLICATION_MODAL);
        dialog.initOwner(dashboardContainer.getScene().getWindow());
        dialog.setResizable(false);
        dialog.setTitle(title);
        VBox box = new VBox(18);
        box.setStyle("-fx-background-color: #181c2f;  -fx-padding: 32px 38px;  -fx-effect: dropshadow(gaussian, #1a6cff33, 24, 0.2, 0, 2);");
        Label titleLabel = new Label(title);
        titleLabel.setStyle("-fx-font-size: 1.3em; -fx-font-weight: bold; -fx-text-fill: #1a6cff; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        Label msgLabel = new Label(message);
        msgLabel.setStyle("-fx-font-size: 1.1em; -fx-text-fill: #fff; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        HBox btnBox = new HBox(18);
        btnBox.setAlignment(javafx.geometry.Pos.CENTER);
        Button yesBtn = new Button("Oui");
        yesBtn.setStyle("-fx-background-color: #1a6cff; -fx-text-fill: #fff; -fx-font-size: 1.1em; -fx-font-weight: bold; -fx-background-radius: 18px; -fx-padding: 10px 28px; -fx-cursor: hand; -fx-effect: dropshadow(gaussian, #1a6cff55, 14, 0.2, 0, 2); -fx-border-width: 0; -fx-border-color: transparent; -fx-alignment: center; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        Button noBtn = new Button("Non");
        noBtn.setStyle("-fx-background-color: #374151; -fx-text-fill: #fff; -fx-font-size: 1.1em; -fx-font-weight: bold; -fx-background-radius: 18px; -fx-padding: 10px 28px; -fx-cursor: hand; -fx-border-width: 1.5px; -fx-border-color: #6b7280; -fx-border-radius: 18px; -fx-alignment: center; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        yesBtn.setOnAction(e -> { result[0] = true; dialog.close(); });
        noBtn.setOnAction(e -> { result[0] = false; dialog.close(); });
        btnBox.getChildren().addAll(yesBtn, noBtn);
        box.getChildren().addAll(titleLabel, msgLabel, btnBox);
        box.setAlignment(javafx.geometry.Pos.CENTER);
        Scene scene = new Scene(box);
        scene.getStylesheets().add(getClass().getResource("/css/userdialog.css").toExternalForm());
        dialog.setScene(scene);
        dialog.centerOnScreen();
        dialog.showAndWait();
        return result[0];
    }

    @FXML
    private void handleLogout() {
        boolean confirmed = showCustomConfirmDialog("Déconnexion", "Êtes-vous sûr de vouloir vous déconnecter ?");
        if (confirmed) {
            try {
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/login.fxml"));
                Scene scene = new Scene(loader.load());
                scene.getStylesheets().add(getClass().getResource("/css/login.css").toExternalForm());
                Stage stage = (Stage) dashboardContainer.getScene().getWindow();
                stage.setScene(scene);
                stage.centerOnScreen();
            } catch (IOException e) {
                e.printStackTrace();
                showErrorMessage("Erreur lors de la déconnexion");
            }
        }
    }

    private void openUserDialog(User user, boolean readOnly) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/user-dialog.fxml"));
            Scene scene = new Scene(loader.load());
            scene.getStylesheets().add(getClass().getResource("/css/userdialog.css").toExternalForm());
            UserDialogController controller = loader.getController();
            controller.setUser(user);
            controller.setReadOnly(readOnly);
            Stage dialogStage = new Stage();
            dialogStage.setTitle(user == null ? "Ajouter un utilisateur" : (readOnly ? "Voir l'utilisateur" : "Modifier l'utilisateur"));
            dialogStage.setScene(scene);
            dialogStage.initModality(Modality.WINDOW_MODAL);
            dialogStage.initOwner(usersTable.getScene().getWindow());
            dialogStage.setResizable(false);
            dialogStage.showAndWait();
            loadUsers();
        } catch (IOException e) {
            e.printStackTrace();
            showErrorMessage("Erreur lors de l'ouverture du dialogue");
        }
    }

    private void openUserDialog(User user) {
        openUserDialog(user, false);
    }

    private void handleViewUser(User user) {
        openUserDialog(user, true);
    }

    private void handleEditUser(User user) {
        openUserDialog(user);
    }

    private void handleDeleteUser(User user) {
        if (user != null) {
            boolean confirmed = showCustomConfirmDialog("Confirmation", "Supprimer l'utilisateur\nÊtes-vous sûr de vouloir supprimer l'utilisateur '" + user.getUsername() + "' ?");
            if (confirmed) {
                userService.deleteUser(user);
                loadUsers();
                showSuccessMessage("Utilisateur supprimé avec succès");
            }
        }
    }

    private void showSuccessMessage(String message) {
        showCustomDialog("Succès", message, false);
    }

    private void showErrorMessage(String message) {
        showCustomDialog("Erreur", message, true);
    }

    private void showCustomDialog(String title, String message, boolean isError) {
        Stage dialog = new Stage();
        dialog.initModality(Modality.APPLICATION_MODAL);
        dialog.initOwner(dashboardContainer.getScene().getWindow());
        dialog.setResizable(false);
        dialog.setTitle(title);

        VBox box = new VBox(18);
        box.setStyle("-fx-background-color: #181c2f;  -fx-padding: 32px 38px; -fx-effect: dropshadow(gaussian, #1a6cff33, 24, 0.2, 0, 2);");
        Label titleLabel = new Label(title);
        titleLabel.setStyle("-fx-font-size: 1.3em; -fx-font-weight: bold; -fx-text-fill: " + (isError ? "#ff3576" : "#1a6cff") + "; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        Label msgLabel = new Label(message);
        msgLabel.setStyle("-fx-font-size: 1.1em; -fx-text-fill: #fff; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        Button okBtn = new Button("OK");
        okBtn.setStyle("-fx-background-color: #1a6cff; -fx-text-fill: #fff; -fx-font-size: 1.1em; -fx-font-weight: bold; -fx-background-radius: 18px; -fx-padding: 10px 28px; -fx-cursor: hand; -fx-effect: dropshadow(gaussian, #1a6cff55, 14, 0.2, 0, 2); -fx-border-width: 0; -fx-border-color: transparent; -fx-alignment: center; -fx-font-family: 'Segoe UI', 'Roboto', sans-serif;");
        okBtn.setOnAction(e -> dialog.close());
        box.getChildren().addAll(titleLabel, msgLabel, okBtn);
        box.setAlignment(javafx.geometry.Pos.CENTER);
        Scene scene = new Scene(box);
        scene.getStylesheets().add(getClass().getResource("/css/userdialog.css").toExternalForm());
        dialog.setScene(scene);
        dialog.centerOnScreen();
        dialog.showAndWait();
    }
}