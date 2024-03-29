package application.view;


import java.net.URL;
import java.util.HashMap;
import java.util.ResourceBundle;
import javafx.scene.control.Label;
import javafx.util.StringConverter;
import org.json.simple.JSONObject;
import application.control.DialogueController;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Spinner;
import javafx.scene.control.SpinnerValueFactory;


/**
 * Controller of the main view
 */
public class MainController implements Initializable  {

    private HashMap<String, Spinner<Double>> spinners = new HashMap<String, Spinner<Double>>(); // Store all the spinners
    private HashMap<String, StackedBarChart<String, Double>> barCharts = new HashMap<String, StackedBarChart<String, Double>>(); // Store all the barCharts
    private HashMap<String, CheckBox> checkBoxs = new HashMap<String, CheckBox>(); // Store all the checkBoxs
    private DialogueController dialogueController; // Store the dialogueController

    @FXML
    CheckBox cbActivite;
    @FXML
    Spinner<Double> spinnerActivite;
    @FXML
    StackedBarChart<String, Double> bcActivite;
    @FXML
    CheckBox cbCO2;
    @FXML
    Spinner<Double> spinnerCO2;
    @FXML
    StackedBarChart<String, Double> bcCO2;
    @FXML
    CheckBox cbHumidite;
    @FXML
    Spinner<Double> spinnerHumidite;
    @FXML
    StackedBarChart<String, Double> bcHumidite;
    @FXML
    CheckBox cbIllumination;
    @FXML
    Spinner<Double> spinnerIllumination;
    @FXML
    StackedBarChart<String, Double> bcIllumination;
    @FXML
    CheckBox cbInfrarouges;
    @FXML
    Spinner<Double> spinnerInfrarouges;
    @FXML
    StackedBarChart<String, Double> bcInfrarouges;
    @FXML
    CheckBox cbPression;
    @FXML
    Spinner<Double> spinnerPression;
    @FXML
    StackedBarChart<String, Double> bcPression;
    @FXML
    CheckBox cbTemperature;
    @FXML
    Spinner<Double> spinnerTemperature;
    @FXML
    StackedBarChart<String, Double> bcTemperature;
    @FXML
    CheckBox cbQualiteAir;
    @FXML
    Spinner<Double> spinnerQualiteAir;
    @FXML
    StackedBarChart<String, Double> bcQualiteAir;
    @FXML
    Label infoLabel;

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
        String value = pfNewValue.matches("-{0,1}\\d{1,}(\\.|,){0,1}\\d*") ?
        pfNewValue.replace(".", ",") : pfOldValue;
        spinners.get(pfKkey).getEditor().setText((value));
    }

    /**
     * getter barCharts
     * @return
     */
    public HashMap<String, StackedBarChart<String, Double>> getBarCharts() {
        return this.barCharts;
    }

    /**
     * getter checkBoxs
     * @return
     */
    public HashMap<String, Spinner<Double>> getSpinners() {
        return this.spinners;
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
        HashMap<String, Integer> maxValues = new HashMap<String, Integer>();
        maxValues.put("activity", 1000);
        maxValues.put("co2", 10000);
        maxValues.put("humidity", 1000);
        maxValues.put("illumination", 1000000);
        maxValues.put("infrared", 100);
        maxValues.put("pressure", 10000);
        maxValues.put("temperature", 100);
        maxValues.put("tvoc", 1000);
        for (String key : checkBoxs.keySet()) {

            // Init checkbox and checkbox listener
            checkBoxs.get(key).setSelected(true);
            checkBoxs.get(key).setOnAction(e -> {
                this.checkBoxListener(key);
                dialogueController.checkBoxListener(key, checkBoxs.get(key).isSelected());
            });

            // Init spinners and spinner listener
            spinners.get(key).setValueFactory(
                    new SpinnerValueFactory.DoubleSpinnerValueFactory(-100, maxValues.get(key), 0, 0.1)
            );

            // Empêche la saisie de valeurs non-numériques
            spinners.get(key).getEditor().textProperty().addListener((observable, oldValue, newValue) -> {
                this.spinnerListener(newValue, oldValue, key);
                dialogueController.spinnerListener(key, newValue);
                this.commitEditorText(spinners.get(key));
            });
        }
    }

    /**
        Load the view configuration from the JSON file.
        @param pfConfigView JSONObject contain the oldConfiguration of the view
        @apiNote MAY NOT WORK SOMETIMES WHEN RESETTING THE DEFAULT CONFIGURATION
            (mostly if the user changed a spinner data by using the keyboard input before)
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
                    this.checkBoxs.get(keyStr2).setSelected((selected));
                    this.checkBoxListener(keyStr2);
                }
                if (keyStr.equals("seuils")) {
                    Double oldValue = (Double) ((JSONObject) pfConfigView.get(keyStr)).get(keyStr2);
                    spinners.get(keyStr2).getValueFactory().setValue(oldValue);
                }
            }
        }
    }

    /**
     * Commit the given spinner value if it has been manually given (using the keyboard)
     * <a href="https://stackoverflow.com/questions/32340476/manually-typing-in-text-in-javafx-spinner-is-not-updating-the-value-unless-user">Source</a>
     */
    private <T> void commitEditorText(Spinner<T> spinner) {
        if (!spinner.isEditable()) return;
        String text = spinner.getEditor().getText();
        SpinnerValueFactory<T> valueFactory = spinner.getValueFactory();
        if (valueFactory != null) {
            StringConverter<T> converter = valueFactory.getConverter();
            if (converter != null) {
                T value = converter.fromString(text);
                valueFactory.setValue(value);
            }
        }
    }

    @FXML
    private void resetConfig() {
        this.dialogueController.resetConfig();
    }

    public void setInfoLabel(String msg) {
        this.infoLabel.setText(msg);
    }
    
}