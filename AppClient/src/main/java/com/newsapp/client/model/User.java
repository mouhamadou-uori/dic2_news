package com.newsapp.client.model;

import javafx.beans.property.*;

public class User {
    private final IntegerProperty id;
    private final StringProperty username;
    private final StringProperty email;
    private final StringProperty role;
    private final StringProperty status;
    private final StringProperty createdDate;

    public User() {
        this.id = new SimpleIntegerProperty();
        this.username = new SimpleStringProperty();
        this.email = new SimpleStringProperty();
        this.role = new SimpleStringProperty();
        this.status = new SimpleStringProperty();
        this.createdDate = new SimpleStringProperty();
    }

    public User(int id, String username, String email, String role, String status, String createdDate) {
        this();
        setId(id);
        setUsername(username);
        setEmail(email);
        setRole(role);
        setStatus(status);
        setCreatedDate(createdDate);
    }

    // Getters and Setters
    public int getId() { return id.get(); }
    public void setId(int id) { this.id.set(id); }
    public IntegerProperty idProperty() { return id; }

    public String getUsername() { return username.get(); }
    public void setUsername(String username) { this.username.set(username); }
    public StringProperty usernameProperty() { return username; }

    public String getEmail() { return email.get(); }
    public void setEmail(String email) { this.email.set(email); }
    public StringProperty emailProperty() { return email; }

    public String getRole() { return role.get(); }
    public void setRole(String role) { this.role.set(role); }
    public StringProperty roleProperty() { return role; }

    public String getStatus() { return status.get(); }
    public void setStatus(String status) { this.status.set(status); }
    public StringProperty statusProperty() { return status; }

    public String getCreatedDate() { return createdDate.get(); }
    public void setCreatedDate(String createdDate) { this.createdDate.set(createdDate); }
    public StringProperty createdDateProperty() { return createdDate; }
}
