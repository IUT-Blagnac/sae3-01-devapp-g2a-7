package application.displayThread;

import application.DialogueController;
import application.data.JSONReader;
import javafx.scene.chart.StackedBarChart;
import org.json.simple.JSONObject;

import java.util.HashMap;

public class ShowData implements Runnable{

    public static final ShowData instance = new ShowData();
    private HashMap<String, StackedBarChart<String, Double>> barCharts;
    private boolean running;


    /**
     * Initialize ShowData
     */
    public void init() {
    }

    @Override
    public void run() {
        while(running) {

        }
    }

    /**
     * Get the instance of JSONReader
     * @return JSONReader
     */
    public static ShowData getInstance() {
        return instance;
    }

    /**
     * Stop the thread of the JSONReader
     */
    public void stop() {
        this.running = false;
    }

    /**
     * Start the thread of the JSONReader
     */
    public void start() {
        this.running = true;
        new Thread(this).start();
    }

    public void setData(JSONObject pfData) {

    }

    public void setBarCharts(HashMap<String, StackedBarChart<String, Double>> barCharts) {
        this.barCharts = barCharts;
    }
}
