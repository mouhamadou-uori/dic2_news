package com.newsapp.client.controller;

import com.newsapp.client.model.User;
import com.newsapp.client.service.UserService;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.*;
import javafx.stage.Stage;
import org.kordamp.ikonli.javafx.FontIcon;
import org.kordamp.ikonli.materialdesign2.*;

import java.net.URL;
import java.util.ResourceBundle;
import javafx.scene.layout.VBox;
import javafx.scene.Scene;

public class UserDialogController implements Initializable {

    @FXML private TextField usernameField;
    @FXML private TextField emailField;
    @FXML private ComboBox<String> roleComboBox;
    @FXML private ComboBox<String> statusComboBox;
    @FXML private Button saveButton;
    @FXML private Button cancelButton;

    private User user;
    private UserService userService;
    private boolean isEditMode = false;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        userService = UserService.getInstance();
        setupUI();
        setupValidation();
    }

    private void setupUI() {
        FontIcon userIcon = new FontIcon(MaterialDesignA.ACCOUNT);
        userIcon.setIconSize(16);

        FontIcon emailIcon = new FontIcon(MaterialDesignE.EMAIL);
        emailIcon.setIconSize(16);

        FontIcon saveIcon = new FontIcon(MaterialDesignC.CONTENT_SAVE);
        saveIcon.setIconSize(16);
        saveButton.setGraphic(saveIcon);

        FontIcon cancelIcon = new FontIcon(MaterialDesignC.CANCEL);
        cancelIcon.setIconSize(16);
        cancelButton.setGraphic(cancelIcon);

        roleComboBox.getItems().addAll("Administrateur", "Éditeur", "Visiteur");
        statusComboBox.getItems().addAll("Actif", "Inactif");

        roleComboBox.setValue("Visiteur");
        statusComboBox.setValue("Actif");
    }

    private void setupValidation() {
        usernameField.textProperty().addListener((obs, oldText, newText) -> validateForm());
        emailField.textProperty().addListener((obs, oldText, newText) -> validateForm());
        roleComboBox.valueProperty().addListener((obs, oldValue, newValue) -> validateForm());
        statusComboBox.valueProperty().addListener((obs, oldValue, newValue) -> validateForm());
    }

    private void validateForm() {
        boolean isValid = !usernameField.getText().trim().isEmpty() &&
                         !emailField.getText().trim().isEmpty() &&
                         emailField.getText().contains("@") &&
                         roleComboBox.getValue() != null &&
                         statusComboBox.getValue() != null;
        
        saveButton.setDisable(!isValid);
    }

    public void setUser(User user) {
        this.user = user;
        this.isEditMode = user != null;
        
        if (isEditMode) {
            usernameField.setText(user.getUsername());
            emailField.setText(user.getEmail());
            roleComboBox.setValue(user.getRole());
            statusComboBox.setValue(user.getStatus());
        }
        
        validateForm();
    }

    public void setReadOnly(boolean readOnly) {
        usernameField.setDisable(readOnly);
        emailField.setDisable(readOnly);
        roleComboBox.setDisable(readOnly);
        statusComboBox.setDisable(readOnly);
        saveButton.setVisible(!readOnly);
        saveButton.setManaged(!readOnly);
    }

    @FXML
    private void handleSave() {
        if (!validateInput()) {
            return;
        }

        if (isEditMode) {
            user.setUsername(usernameField.getText().trim());
            user.setEmail(emailField.getText().trim());
            user.setRole(roleComboBox.getValue());
            user.setStatus(statusComboBox.getValue());
            userService.updateUser(user);
        } else {
            User newUser = new User();
            newUser.setUsername(usernameField.getText().trim());
            newUser.setEmail(emailField.getText().trim());
            newUser.setRole(roleComboBox.getValue());
            newUser.setStatus(statusComboBox.getValue());
            userService.addUser(newUser);
        }

        closeDialog();
    }

    @FXML
    private void handleCancel() {
        closeDialog();
    }

    private boolean validateInput() {
        String username = usernameField.getText().trim();
        String email = emailField.getText().trim();

        if (username.isEmpty()) {
            showError("Le nom d'utilisateur est requis");
            return false;
        }

        if (email.isEmpty()) {
            showError("L'email est requis");
            return false;
        }

        if (!email.matches("^[A-Za-z0-9+_.-]+@(.+)$")) {
            showError("Format d'email invalide");
            return false;
        }

        boolean usernameExists = userService.getAllUsers().stream()
                .anyMatch(u -> u.getUsername().equals(username) && 
                         (!isEditMode || u.getId() != user.getId()));

        if (usernameExists) {
            showError("Ce nom d'utilisateur existe déjà");
            return false;
        }

        return true;
    }

    private void showError(String message) {
        showCustomDialog("Erreur de validation", message, true);
    }

    private void showCustomDialog(String title, String message, boolean isError) {
        Stage dialog = new Stage();
        dialog.initModality(javafx.stage.Modality.APPLICATION_MODAL);
        dialog.initOwner(saveButton.getScene().getWindow());
        dialog.setResizable(false);
        dialog.setTitle(title);

        VBox box = new VBox(18);
        box.setStyle("-fx-background-color: #181c2f; -fx-background-radius: 18px; -fx-padding: 32px 38px; -fx-effect: dropshadow(gaussian, #1a6cff33, 24, 0.2, 0, 2);");
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

    private void closeDialog() {
        Stage stage = (Stage) saveButton.getScene().getWindow();
        stage.close();
    }
}
