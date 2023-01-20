package application.control;

import javafx.application.Platform;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.Spinner;
import org.json.simple.JSONObject;
import java.util.*;
import javafx.scene.chart.XYChart;

/**
 * 
 * ShowData class is used to display data in a StackedBarChart format.
 * 
 * It is a singleton class, with a static final instance field, and a
 * getInstance() method to access it.
 * 
 * The class has methods to initialize, set and update data, set StackedBarChart
 * and threshold values, and update threshold values.
 */
public class ShowData {

    public static final ShowData instance = new ShowData(); // TODO
    private HashMap<String, StackedBarChart<String, Double>> barCharts; // TODO
    private HashMap<String, Double> seuils; // TODO

    /**
     * Get the instance of ShowData
     * 
     * @return ShowData
     */
    public static ShowData getInstance() {
        return instance;
    }

    /**
     * Calls the function that updates the bar charts, putting it in the javaFX
     * thread
     * 
     * @param pfData JSONObject containing data for the chart
     */
    public void setData(JSONObject pfData) {
        Platform.runLater(() -> updateBarCharts(pfData));
    }

    /**
     * Updates the StackedBarChart with data from the JSONObject
     * 
     * @param pfData JSONObject containing data for the chart
     */
    public void updateBarCharts(JSONObject pfData) {
        for (Object key : ((JSONObject) pfData.get("donnees")).keySet()) {
            System.out.println(key);
            Double data = Double.parseDouble(((JSONObject) pfData.get("donnees")).get(key).toString());
            StackedBarChart<String, Double> barChart = this.barCharts.get(key);
            if (!((JSONObject) pfData.get("seuils")).get(key).equals(true)) {
                XYChart.Series<String, Double> series = new XYChart.Series<>();
                series.getData().add(new XYChart.Data<>("", data));
                barChart.getData().clear();
                barChart.getData().addAll(series);
            } else {
                System.out.println(key);
                Double seuil = this.seuils.get(key);
                System.out.println(seuil);
                XYChart.Series<String, Double> series1 = new XYChart.Series<>();
                XYChart.Series<String, Double> series2 = new XYChart.Series<>();
                series1.getData().add(new XYChart.Data<>("", seuil));
                series2.getData().add(new XYChart.Data<>("", data - seuil));
                barChart.getData().clear();
                barChart.getData().addAll(series1, series2);
            }
        }
    }

    /**
     * Sets the StackedBarChart objects to be used in the class
     * 
     * @param barCharts HashMap of StackedBarChart objects, keyed by a String
     */
    public void setBarCharts(HashMap<String, StackedBarChart<String, Double>> barCharts) {
        this.barCharts = barCharts;
    }

    /**
     * Sets the threshold values for the chart
     * 
     * @param spinners HashMap of Spinner objects, containing the thresholds
     */
    public void setSeuils(HashMap<String, Spinner<Double>> spinners) {
        this.seuils = new HashMap<String, Double>();
        for (String key : spinners.keySet()) {
            seuils.put(key, spinners.get(key).getValue());
        }
    }

    /**
     * Updates the threshold value for a specified key
     * 
     * @param key   String key for the threshold value
     * @param value String value of the threshold
     */
    public void updateSeuil(String key, String value) {
        if (value.matches("-{0,1}\\d*(\\.|,){0,1}\\d*")) {
            this.seuils.put(key, (Double.parseDouble(value.replace(",", "."))));
        }
    }

}
