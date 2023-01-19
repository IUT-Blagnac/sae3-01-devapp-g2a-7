package application.controller;


import java.net.URL;
import java.util.HashMap;
import java.util.ResourceBundle;

import org.json.simple.JSONObject;

import application.DialogueController;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Spinner;
import javafx.scene.control.SpinnerValueFactory;


public class MainController implements Initializable  {

    private HashMap<String, Spinner<Double>> spinners = new HashMap<String, Spinner<Double>>();
    private HashMap<String, StackedBarChart<String, Float>> barCharts = new HashMap<String, StackedBarChart<String, Float>>();
    private HashMap<String, CheckBox> checkBoxs = new HashMap<String, CheckBox>();
    private DialogueController dialogueController;

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

    /**
     * Initialize the controller
     */
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
        
    }

    /**
     * Listener of checkbox
     * @param pfKey
     */
    public void checkBoxListener(String pfKey) {
        if (checkBoxs.get(pfKey).isSelected()) {
            spinners.get(pfKey).setDisable(false);
            barCharts.get(pfKey).setPrefWidth(150);
            barCharts.get(pfKey).setVisible(true);
        } else {
            spinners.get(pfKey).setDisable(true);
            barCharts.get(pfKey).setPrefWidth(0);
            barCharts.get(pfKey).setVisible(false);
        }
    }

    /**
     * Listener of spinner
     * @param pfNewValue
     * @param pfOldValue
     * @param pfKkey
     */
    public void spinnerListener(String pfNewValue, String pfOldValue, String pfKkey) {
        String value = pfNewValue.matches("-{0,1}\\d*(\\.|,){0,1}\\d*") ?
        pfNewValue.replace(".", ",") : pfOldValue;
        spinners.get(pfKkey).getEditor().setText((value));
    }

    /**
     * Setter dialogueController
     * @param pfDialogueController
     */
    public void setDialogueController(DialogueController pfDialogueController) {
        this.dialogueController = pfDialogueController;
    }

    /**
     * Initialisation of elements and listeners
     */
    public void init() {
        // Init checkbox and checkbox listener
        for (String key : checkBoxs.keySet()) {
            checkBoxs.get(key).setSelected(true);
            checkBoxs.get(key).setOnAction(e -> {
                this.checkBoxListener(key);
                dialogueController.checkBoxListener(key, checkBoxs.get(key).isSelected());
            });
        }

        // Init spinners and spinner listener
        for (String key : spinners.keySet()) {
            spinners.get(key).setValueFactory(
                new SpinnerValueFactory.DoubleSpinnerValueFactory(-9999, 9999, 0, 0.1)
            );

            // Empêche la saisie de valeurs non-numériques
            spinners.get(key).getEditor().textProperty().addListener((observable, oldValue, newValue) -> {
                this.spinnerListener(newValue, oldValue, key);
                dialogueController.spinnerListener(key, newValue);
            });
        }
    }

    /**
     * Show datas // TODO
     * @param pfData
     */
    public void showData(JSONObject pfData) {

    }

    /**
        Load the view configuration from the JSON file.
        @param pfConfigView JSONObject contain the oldConfiguration of the view
     */
    public void loadView(JSONObject pfConfigView) {
        for ( Object key : pfConfigView.keySet()) {
            // cast key to String
            String keyStr = (String) key;
            for( Object key2 : ((JSONObject) pfConfigView.get(keyStr)).keySet()) {
                // cast key to String
                String keyStr2 = (String) key2;
                if (keyStr.equals("donnees")) {
                    Boolean selected = (Boolean) ((JSONObject) pfConfigView.get(keyStr)).get(keyStr2);
                    if (!selected) {
                        this.checkBoxs.get(keyStr2).setSelected(false);
                        checkBoxListener(keyStr2);
                    }
                }
                if (keyStr.equals("seuils")) {
                    Double oldValue = (Double) ((JSONObject) pfConfigView.get(keyStr)).get(keyStr2);
                    spinners.get(keyStr2).getValueFactory().setValue(oldValue);
                }
            }
        }
    }
    
}