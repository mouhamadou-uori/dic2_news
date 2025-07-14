package com.newsapp.client.controller;

import com.newsapp.client.service.UserService;
import javafx.animation.FadeTransition;
import javafx.animation.ScaleTransition;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.input.KeyCode;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import javafx.util.Duration;
import org.kordamp.ikonli.javafx.FontIcon;
import org.kordamp.ikonli.materialdesign2.MaterialDesignA;
import org.kordamp.ikonli.materialdesign2.MaterialDesignL;
import org.kordamp.ikonli.materialdesign2.MaterialDesignC;

import java.io.IOException;
import java.net.URL;
import java.util.ResourceBundle;

public class LoginController implements Initializable {

    @FXML
    private VBox loginContainer;
    @FXML
    private TextField usernameField;
    @FXML
    private PasswordField passwordField;
    @FXML
    private Button loginButton;
    @FXML
    private Label errorLabel;

    private UserService userService;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        userService = UserService.getInstance();
        setupUI();
        setupEventHandlers();
        playEntryAnimation();
    }

    private void setupUI() {
        FontIcon userIcon = new FontIcon(MaterialDesignA.ACCOUNT);
        userIcon.setIconSize(20);

        FontIcon lockIcon = new FontIcon(MaterialDesignL.LOCK);
        lockIcon.setIconSize(20);
        usernameField.setText("admin");
        passwordField.setText("admin123");
    }

    private void setupEventHandlers() {
        passwordField.setOnKeyPressed(event -> {
            if (event.getCode() == KeyCode.ENTER) {
                handleLogin();
            }
        });

        usernameField.setOnKeyPressed(event -> {
            if (event.getCode() == KeyCode.ENTER) {
                passwordField.requestFocus();
            }
        });
    }

    private void playEntryAnimation() {
        FadeTransition fadeIn = new FadeTransition(Duration.millis(800), loginContainer);
        fadeIn.setFromValue(0.0);
        fadeIn.setToValue(1.0);

        ScaleTransition scaleIn = new ScaleTransition(Duration.millis(800), loginContainer);
        scaleIn.setFromX(0.8);
        scaleIn.setFromY(0.8);
        scaleIn.setToX(1.0);
        scaleIn.setToY(1.0);

        fadeIn.play();
        scaleIn.play();
    }

    @FXML
    private void handleLogin() {
        String username = usernameField.getText().trim();
        String password = passwordField.getText();

        if (username.isEmpty() || password.isEmpty()) {
            showError("Veuillez saisir votre nom d'utilisateur et mot de passe");
            return;
        }

        if (userService.authenticate(username, password)) {
            openDashboard();
        } else {
            showError("Nom d'utilisateur ou mot de passe incorrect");
            shakeLoginButton();
        }
    }

    private void showError(String message) {
        errorLabel.setText(message);
        errorLabel.setVisible(true);

        FadeTransition fadeIn = new FadeTransition(Duration.millis(300), errorLabel);
        fadeIn.setFromValue(0.0);
        fadeIn.setToValue(1.0);
        fadeIn.play();
    }

    private void shakeLoginButton() {
        ScaleTransition shake = new ScaleTransition(Duration.millis(100), loginButton);
        shake.setFromX(1.0);
        shake.setToX(0.95);
        shake.setCycleCount(4);
        shake.setAutoReverse(true);
        shake.play();
    }

    private void openDashboard() {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/dashboard.fxml"));
            Scene scene = new Scene(loader.load());
            scene.getStylesheets().add(getClass().getResource("/css/dashboard.css").toExternalForm());

            Stage currentStage = (Stage) loginButton.getScene().getWindow();
            Stage dashboardStage = new Stage();

            dashboardStage.setTitle("DIC2_NEWS");
            dashboardStage.setScene(scene);
            dashboardStage.setMaximized(true);
            dashboardStage.show();

            currentStage.close();

        } catch (IOException e) {
            e.printStackTrace();
            showError("Erreur lors de l'ouverture du dashboard");
        }
    }

    @FXML
    private void handleClose() {
        System.exit(0);
    }
}
