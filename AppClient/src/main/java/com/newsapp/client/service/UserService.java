package com.newsapp.client.service;

import com.newsapp.client.model.User;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.List;

public class UserService {
    private static UserService instance;
    private ObservableList<User> users;
    private int nextId = 1;

    private UserService() {
        users = FXCollections.observableArrayList();
        initializeData();
    }

    public static UserService getInstance() {
        if (instance == null) {
            instance = new UserService();
        }
        return instance;
    }

    private void initializeData() {
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm");
        
        users.addAll(
            new User(nextId++, "admin", "admin@newsapp.com", "Administrateur", "Actif", 
                    LocalDateTime.now().minusDays(30).format(formatter)),
            new User(nextId++, "editor1", "editor1@newsapp.com", "Éditeur", "Actif", 
                    LocalDateTime.now().minusDays(15).format(formatter)),
            new User(nextId++, "editor2", "editor2@newsapp.com", "Éditeur", "Inactif", 
                    LocalDateTime.now().minusDays(10).format(formatter)),
            new User(nextId++, "visitor1", "visitor1@newsapp.com", "Visiteur", "Actif", 
                    LocalDateTime.now().minusDays(5).format(formatter)),
            new User(nextId++, "visitor2", "visitor2@newsapp.com", "Visiteur", "Actif", 
                    LocalDateTime.now().minusDays(2).format(formatter))
        );
    }

    public ObservableList<User> getAllUsers() {
        return users;
    }

    public void addUser(User user) {
        user.setId(nextId++);
        user.setCreatedDate(LocalDateTime.now().format(DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm")));
        users.add(user);
    }

    public void updateUser(User user) {
        for (int i = 0; i < users.size(); i++) {
            if (users.get(i).getId() == user.getId()) {
                users.set(i, user);
                break;
            }
        }
    }

    public void deleteUser(User user) {
        users.remove(user);
    }

    public boolean authenticate(String username, String password) {
        // Simulation d'authentification statique
        return "admin".equals(username) && "admin123".equals(password);
    }
}
