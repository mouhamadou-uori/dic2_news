module newsapp.client {
    requires javafx.controls;
    requires javafx.fxml;
    requires org.kordamp.ikonli.core;
    requires org.kordamp.ikonli.javafx;
    requires org.kordamp.ikonli.materialdesign2;
    requires com.fasterxml.jackson.databind;
    requires java.xml.bind;
    
    exports com.newsapp.client;
    exports com.newsapp.client.controller;
    exports com.newsapp.client.model;
    exports com.newsapp.client.service;
    opens com.newsapp.client.controller to javafx.fxml;
}
