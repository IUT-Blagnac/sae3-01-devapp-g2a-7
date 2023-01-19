package application;

import javafx.application.Platform;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.control.Spinner;
import org.json.simple.JSONObject;

import java.util.*;

import javafx.application.Application;
import javafx.collections.FXCollections;
import javafx.scene.Group;
import javafx.scene.Scene;
import javafx.scene.chart.CategoryAxis;
import javafx.stage.Stage;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.StackedBarChart;
import javafx.scene.chart.XYChart;

public class ShowData {

    public static final ShowData instance = new ShowData();
    private HashMap<String, StackedBarChart<String, Double>> barCharts;
    private HashMap<String, Double> seuils ;
    private boolean running;


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


    public void setData(JSONObject pfData) {
        Platform.runLater(() -> updateBarCharts (pfData));
    }

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

    public void setBarCharts(HashMap<String, StackedBarChart<String, Double>> barCharts) {
        this.barCharts = barCharts;
    }

    public void setSeuils(HashMap<String, Spinner<Double>> spinners) {
        this.seuils = new HashMap<String, Double>();
        for (String key : spinners.keySet()) {
            seuils.put(key, spinners.get(key).getValue()) ;
        }
    }

    public void updateSeuil(String key, String value) {
        this.seuils.put(key, Double.parseDouble(value));
    }
}
