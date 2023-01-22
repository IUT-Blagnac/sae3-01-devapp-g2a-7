package application.model;


import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.HashMap;

import org.json.simple.JSONObject;


/**
 * Write the data in a config.json file
 */
public class JSONWriter {

    private JSONObject dataToCollect; // Store the data to collect
    private final static JSONWriter instance = new JSONWriter(); // Singleton

    /**
     * Constructor
     */
    private JSONWriter() {
        this.dataToCollect = new JSONObject();
    }

    /**
     * Get the instance of JSONController
     * @return JSONController instance
     */
    public static JSONWriter getInstance() {
        return instance;
    }

    /**
     * Initialize the JSONController
     */
    @SuppressWarnings("unchecked")
    public void init() {
        this.dataToCollect.put("seuils", new JSONObject());
        this.dataToCollect.put("donnees", new JSONObject());
    }

    /**
     * Write the configuration data in a config.json file
     */
    public void writeData() {
        try {
            String path = System.getProperty("user.dir").replace("codeJava\\App", "codePython\\config.json"); // TO MODIFY
            File file = new File(path);
            if (!file.exists()) {
                file.createNewFile();
            }
            // Write this.dataToCollect.toJSONString() in file
            FileWriter fileWriter = new FileWriter(file);
            fileWriter.write(this.dataToCollect.toJSONString());
            fileWriter.flush();
            fileWriter.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    /**
     * Set the default seuils to the max bounds of the corresponding bar chart
     */
    @SuppressWarnings("unchecked")
    public void setSeuilsByDefault(String keys[]) {
        HashMap<String, Double> defaultSeuils = new HashMap<String, Double>();
        defaultSeuils.put("activity", 10.0);
        defaultSeuils.put("co2", 10000.0);
        defaultSeuils.put("humidity", 1000.0);
        defaultSeuils.put("illumination", 1000000.0);
        defaultSeuils.put("infrared", 100.0);
        defaultSeuils.put("pressure", 10000.0);
        defaultSeuils.put("temperature", 100.0);
        defaultSeuils.put("tvoc", 1000.0);
        for (String key : keys) {
            ((JSONObject) this.dataToCollect.get("seuils")).put(key, defaultSeuils.get(key));
        }
    }

    /**
     * Set the donnees to true
     */
    @SuppressWarnings("unchecked")
    public void setDonneesByDefault(String keys[]) {
        for (String key : keys) {
            ((JSONObject) this.dataToCollect.get("donnees")).put(key, true);
        }
    }

    /**
     * Get the data collected
     * @return JSONObject dataToCollect the data collected
     */
    public JSONObject getDataToCollect() {
        return this.dataToCollect;
    }

    public void setDataToCollect(JSONObject pfData) {
        this.dataToCollect = pfData;
    }

    /**
     * Update the data to collect
     * @param key the key of the data to collect
     * @param value true or false to collect the data
     */
    @SuppressWarnings("unchecked")
    public void updateDonnees(String key, boolean value) {
        ((JSONObject) this.dataToCollect.get("donnees")).put(key, value);
        this.writeData();
    }

    /**
     * Update the threshold to collect
     * @param key the key of the data to collect
     * @param value the value of the threshold to collect
     */
    @SuppressWarnings("unchecked")
    public void updateSeuil(String key, Object value) {
        if (value.equals("")) {
            ((JSONObject) this.dataToCollect.get("seuils")).put(key, null);
        } else {
            String replacedValue = ((String) value).replace(",", ".");
            try {
                ((JSONObject) this.dataToCollect.get("seuils")).put(key, Double.parseDouble(replacedValue));
            } catch (Exception e) {}
        }
        this.writeData();
    }
    
}
