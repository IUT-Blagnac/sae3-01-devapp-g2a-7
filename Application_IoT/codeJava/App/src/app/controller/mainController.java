package app.controller;


import java.net.URL;
import java.util.ArrayList;
import java.util.ResourceBundle;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Spinner;
import javafx.scene.control.SpinnerValueFactory;


public class mainController implements Initializable  {

    private ArrayList<Spinner<Double>> spinners = new ArrayList<Spinner<Double>>();

    @FXML
    StackedBarChart<String, Float> barChart;

    @FXML
    CheckBox activite;

    @FXML
    Spinner<Double> seuilActivite;

    @FXML
    CheckBox CO2;

    @FXML
    Spinner<Double> seuilCO2;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        spinners.add(seuilActivite);
        spinners.add(seuilCO2);
        for (Spinner<Double> spinner : spinners) {
            spinner.setValueFactory(
                    new SpinnerValueFactory.DoubleSpinnerValueFactory(-9999, 9999, 0, 0.1)
            );
            // emêche la saisie de valeurs non numériques
            spinner.getEditor().textProperty().addListener((observable, oldValue, newValue) -> {
                if (!newValue.matches("-{0,1}\\d*\\.\\d*")) {
                    spinner.getEditor().setText(oldValue);
                }
            });
        }
    }
    
}
