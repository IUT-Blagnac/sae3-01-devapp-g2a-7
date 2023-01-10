package app.controller.threads;

import java.util.HashMap;

import org.json.simple.JSONObject;

import javafx.scene.control.CheckBox;
import javafx.scene.control.Spinner;

public class JSONThread implements Runnable {

    private boolean running;
    private HashMap<String, CheckBox> checkBoxs;
    private HashMap<String, Spinner<Double>> spinners;
    private JSONObject dataCollected;
    private JSONObject dataToCollect;

    @SuppressWarnings("unchecked")
    public JSONThread(HashMap<String, CheckBox> pfCheckBoxs, HashMap<String, Spinner<Double>> pfSpinners) {
        this.running = true;
        this.checkBoxs = pfCheckBoxs;
        this.spinners = pfSpinners;
        this.dataCollected = new JSONObject();
        this.dataToCollect = new JSONObject();
        this.dataToCollect.put("seuil", new JSONObject());
    }

    @Override
    public void run() {
        while(this.running) {

        }
    }

    public void stop() {
        this.running = false;
    }

    private void writeData() {

    }

    private JSONObject readData() {
        return null;
    }

    @SuppressWarnings("unchecked")
    private void setCheckBoxListener() {
        for (String key : this.checkBoxs.keySet()) {
            this.checkBoxs.get(key).setOnAction(e -> {
                if (this.checkBoxs.get(key).isSelected()) {
                    this.dataToCollect.put(key, this.spinners.get(key).getValue());
                } else {
                    this.dataToCollect.remove(key);
                }
            });
        }
    }

    @SuppressWarnings("unchecked")
    private void setSpinnerListener() {
        for (String key : this.spinners.keySet()) {
            this.spinners.get(key).valueProperty().addListener((obs, oldValue, newValue) -> {
                if (this.checkBoxs.get(key).isSelected()) {
                    ((JSONObject) this.dataCollected.get("seuil")).put(key, newValue);
                }
            });
        }
    }
    
}
