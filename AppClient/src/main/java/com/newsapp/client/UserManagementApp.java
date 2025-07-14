package com.newsapp.client;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import javafx.stage.StageStyle;

public class UserManagementApp extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {
        FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/login.fxml"));
        Scene scene = new Scene(loader.load());
        scene.getStylesheets().add(getClass().getResource("/css/login.css").toExternalForm());

        primaryStage.setTitle("DIC2_NEWS");
        primaryStage.setScene(scene);
        primaryStage.initStyle(StageStyle.UNDECORATED);
        primaryStage.setResizable(false);
        primaryStage.centerOnScreen();

        try {
            primaryStage.getIcons().add(new Image(getClass().getResourceAsStream("/images/app-icon.png")));
        } catch (Exception e) {
        }

        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}
