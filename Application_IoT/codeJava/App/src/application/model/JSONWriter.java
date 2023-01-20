package application.model;


import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import org.json.simple.JSONObject;
import sun.misc.FileURLMapper;


/**
 * TODO
 */
public class JSONWriter {

    private JSONObject dataToCollect; // TODO
    private final static JSONWriter instance = new JSONWriter(); // TODO

    /**
     * Constructor
     */
    private JSONWriter() {
        this.dataToCollect = new JSONObject();
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
    public void init() {
        this.dataToCollect.put("seuils", new JSONObject());
        this.dataToCollect.put("donnees", new JSONObject());
    }

    /**
     * Write the configuration data in a config.json file
     */
    public void writeData() {
        try {
            File file = new File(System.getProperty("user.dir") + "/../../codePython/config.json");
            System.out.println(file.getPath());
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
    public void setSeuilsByDefault(String keys[]) {
        for (String key : keys) {
            ((JSONObject) this.dataToCollect.get("seuils")).put(key, 0.0);
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
     * @return JSONObject
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
            ((JSONObject) this.dataToCollect.get("seuils")).put(key, Double.parseDouble(replacedValue));
        }
        this.writeData();
    }
    
}
