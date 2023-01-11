package application.data;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;

import org.json.simple.JSONObject;

public class JSONWriter {

    private JSONObject dataToCollect;
    private String[] keys;
    private final static JSONWriter instance = new JSONWriter();

    /**
     * Constructor
     */
    private JSONWriter() {
        this.dataToCollect = new JSONObject();
        this.keys = new String[] {
            "activity",
            "co2",
            "humidity",
            "illumination",
            "infrared",
            "pressure",
            "temperature",
            "tvoc"
        };
    }

    /**
     * Get the instance of JSONController
     * @return JSONController
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
        this.setSeuilByDefault();
        this.setDonneesByDefault();
        this.writeData();
    }

    /**
     * Write the configuration data in a config.json file
     */
    private void writeData() {
        try {
            File file = new File(System.getProperty("user.dir") + "/Application_IoT/codePython/config.json");
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
     * Set the seuils to 0
     */
    @SuppressWarnings("unchecked")
    private void setSeuilByDefault() {
        for (String key : keys) {
            ((JSONObject) this.dataToCollect.get("seuils")).put(key, 0);
        }
    }

    /**
     * Set the donnees to true
     */
    @SuppressWarnings("unchecked")
    private void setDonneesByDefault() {
        for (String key : keys) {
            ((JSONObject) this.dataToCollect.get("donnees")).put(key, true);
        }
    }

    /**
     * Get the data collected
     * @return JSONObject
     */
    public String getDataToCollect() {
        return this.dataToCollect.toJSONString();
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
            ((JSONObject) this.dataToCollect.get("seuils")).put(key, Double.parseDouble((String) value));
        }
        this.writeData();
    }
    
}
