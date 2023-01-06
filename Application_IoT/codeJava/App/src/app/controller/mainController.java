package app.controller;


import java.net.URL;
import java.util.ResourceBundle;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Spinner;


public class mainController implements Initializable  {

    @FXML
    StackedBarChart barChart;

    @FXML
    CheckBox activite;

    @FXML
    Spinner seuilActivite;

    @FXML
    CheckBox CO2;

    @FXML
    Spinner seuilCO2;

    @Override
    public void initialize(URL location, ResourceBundle resources) {}
    
}
