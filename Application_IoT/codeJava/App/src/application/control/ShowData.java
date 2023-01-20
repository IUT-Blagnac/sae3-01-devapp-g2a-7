package application.control;


import javafx.application.Platform;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.Spinner;
import org.json.simple.JSONObject;
import java.util.*;
import javafx.scene.chart.XYChart;


/**
 * TODO
 */
public class ShowData {

    public static final ShowData instance = new ShowData(); // TODO
    private HashMap<String, StackedBarChart<String, Double>> barCharts; // TODO
    private HashMap<String, Double> seuils ; // TODO
    private boolean running; // TODO

    /**
     * Initialize ShowData
     */
    public void init() {
    }

    /**
     * Get the instance of ShowData
     * @return ShowData
     */
    public static ShowData getInstance() {
        return instance;
    }

    /**
     * TODO
     * @param pfData
     */
    public void setData(JSONObject pfData) {
        Platform.runLater(() -> updateBarCharts(pfData));
    }

    /**
     * TODO
     * @param pfData
     */
    public void updateBarCharts (JSONObject pfData) {
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
     * TODO
     * @param barCharts
     */
    public void setBarCharts(HashMap<String, StackedBarChart<String, Double>> barCharts) {
        this.barCharts = barCharts;
    }

    /**
     * TODO
     * @param spinners
     */
    public void setSeuils(HashMap<String, Spinner<Double>> spinners) {
        this.seuils = new HashMap<String, Double>();
        for (String key : spinners.keySet()) {
            seuils.put(key, spinners.get(key).getValue()) ;
        }
    }

    /**
     * TODO
     * @param key
     * @param value
     */
    public void updateSeuil(String key, String value) {
        if (value.matches("-{0,1}\\d*(\\.|,){0,1}\\d*")) {
            this.seuils.put(key, Double.parseDouble(value));
        }
    }

}
