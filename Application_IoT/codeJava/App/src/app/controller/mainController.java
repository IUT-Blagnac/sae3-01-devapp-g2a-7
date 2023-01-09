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
    private ArrayList<StackedBarChart> barCharts = new ArrayList<StackedBarChart>();

    @FXML
    CheckBox CBActivite;
    @FXML
    Spinner<Double> spinnerActivite;
    @FXML
    StackedBarChart<String, Float> BCActivite;
    @FXML
    CheckBox CBCO2;
    @FXML
    Spinner<Double> spinnerCO2;
    @FXML
    StackedBarChart<String, Float> BCCO2;
    @FXML
    CheckBox CBHumidite;
    @FXML
    Spinner<Double> spinnerHumidite;
    @FXML
    StackedBarChart<String, Float> BCHumidite;
    @FXML
    CheckBox CBIllumination;
    @FXML
    Spinner<Double> spinnerIllumination;
    @FXML
    StackedBarChart<String, Float> BCIllumination;
    @FXML
    CheckBox CBInfrarouges;
    @FXML
    Spinner<Double> spinnerInfrarouges;
    @FXML
    StackedBarChart<String, Float> BCInfrarouges;
    @FXML
    CheckBox CBPression;
    @FXML
    Spinner<Double> spinnerPression;
    @FXML
    StackedBarChart<String, Float> BCPression;
    @FXML
    CheckBox CBTemperature;
    @FXML
    Spinner<Double> spinnerTemperature;
    @FXML
    StackedBarChart<String, Float> BCTemperature;
    @FXML
    CheckBox CBQualiteAir;
    @FXML
    Spinner<Double> spinnerQualiteAir;
    @FXML
    StackedBarChart<String, Float> BCQualiteAir;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        // Liste des spinners
        spinners.add(spinnerActivite);
        spinners.add(spinnerCO2);
        spinners.add(spinnerHumidite);
        spinners.add(spinnerIllumination);
        spinners.add(spinnerInfrarouges);
        spinners.add(spinnerPression);
        spinners.add(spinnerTemperature);
        spinners.add(spinnerQualiteAir);
        // Initialisation des spinners
        for (Spinner<Double> spinner : spinners) {
            spinner.setValueFactory(
                new SpinnerValueFactory.DoubleSpinnerValueFactory(-9999, 9999, 0, 0.1)
            );
            // Empêche la saisie de valeurs non-numériques
            spinner.getEditor().textProperty().addListener((observable, oldValue, newValue) -> {
                spinner.getEditor().setText((
                    newValue.matches("-{0,1}\\d*(\\.|,){0,1}\\d*") ?
                        newValue.replace(".", ",") : oldValue
                ));
            });
        }
        // Liste des barCharts
        barCharts.add(BCActivite);
        barCharts.add(BCCO2);
        barCharts.add(BCHumidite);
        barCharts.add(BCIllumination);
        barCharts.add(BCInfrarouges);
        barCharts.add(BCPression);
        barCharts.add(BCTemperature);
        barCharts.add(BCQualiteAir);
    }
    
}
