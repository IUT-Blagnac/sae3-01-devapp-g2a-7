package app.controller;


import java.net.URL;
import java.util.HashMap;
import java.util.ResourceBundle;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Spinner;
import javafx.scene.control.SpinnerValueFactory;


public class mainController implements Initializable  {

    private HashMap<String, Spinner<Double>> spinners = new HashMap<String, Spinner<Double>>();
    private HashMap<String, StackedBarChart<String, Float>> barCharts = new HashMap<String, StackedBarChart<String, Float>>();
    private HashMap<String, CheckBox> checkBoxs = new HashMap<String, CheckBox>();

    @FXML
    CheckBox cbActivite;
    @FXML
    Spinner<Double> spinnerActivite;
    @FXML
    StackedBarChart<String, Float> bcActivite;
    @FXML
    CheckBox cbCO2;
    @FXML
    Spinner<Double> spinnerCO2;
    @FXML
    StackedBarChart<String, Float> bcCO2;
    @FXML
    CheckBox cbHumidite;
    @FXML
    Spinner<Double> spinnerHumidite;
    @FXML
    StackedBarChart<String, Float> bcHumidite;
    @FXML
    CheckBox cbIllumination;
    @FXML
    Spinner<Double> spinnerIllumination;
    @FXML
    StackedBarChart<String, Float> bcIllumination;
    @FXML
    CheckBox cbInfrarouges;
    @FXML
    Spinner<Double> spinnerInfrarouges;
    @FXML
    StackedBarChart<String, Float> bcInfrarouges;
    @FXML
    CheckBox cbPression;
    @FXML
    Spinner<Double> spinnerPression;
    @FXML
    StackedBarChart<String, Float> bcPression;
    @FXML
    CheckBox cbTemperature;
    @FXML
    Spinner<Double> spinnerTemperature;
    @FXML
    StackedBarChart<String, Float> bcTemperature;
    @FXML
    CheckBox cbQualiteAir;
    @FXML
    Spinner<Double> spinnerQualiteAir;
    @FXML
    StackedBarChart<String, Float> bcQualiteAir;

    @Override
    public void initialize(URL location, ResourceBundle resources) {
        // Liste des spinners
        spinners.put("activity",spinnerActivite);
        spinners.put("co2", spinnerCO2);
        spinners.put("humidity", spinnerHumidite);
        spinners.put("illumination", spinnerIllumination);
        spinners.put("infrared", spinnerInfrarouges);
        spinners.put("pressure", spinnerPression);
        spinners.put("temperature", spinnerTemperature);
        spinners.put("tvoc", spinnerQualiteAir);

        // Liste des barCharts
        barCharts.put("activity", bcActivite);
        barCharts.put("co2", bcCO2);
        barCharts.put("humidity", bcHumidite);
        barCharts.put("illumination", bcIllumination);
        barCharts.put("infrared", bcInfrarouges);
        barCharts.put("pressure", bcPression);
        barCharts.put("temperature", bcTemperature);
        barCharts.put("tvoc", bcQualiteAir);

        // Liste des checkBoxs
        checkBoxs.put("activity", cbActivite);
        checkBoxs.put("co2", cbCO2);
        checkBoxs.put("humidity", cbHumidite);
        checkBoxs.put("illumination", cbIllumination);
        checkBoxs.put("infrared", cbInfrarouges);
        checkBoxs.put("pressure", cbPression);
        checkBoxs.put("temperature", cbTemperature);
        checkBoxs.put("tvoc", cbQualiteAir);

        // Initialisation des spinners
        for (Spinner<Double> spinner : spinners.values()) {
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

        // Check is checkbox is active, if yes enable spinner and visible barchart else disable
        for (String key : checkBoxs.keySet()) {
            checkBoxs.get(key).setSelected(true);
            checkBoxs.get(key).setOnAction(e -> {
                if (checkBoxs.get(key).isSelected()) {
                    spinners.get(key).setDisable(false);
                    barCharts.get(key).setPrefWidth(150);
                    barCharts.get(key).setVisible(true);
                } else {
                    spinners.get(key).setDisable(true);
                    barCharts.get(key).setPrefWidth(0);
                    barCharts.get(key).setVisible(false);
                }
            });
        }

        // Threads pour JSON
        
    }
    
}
