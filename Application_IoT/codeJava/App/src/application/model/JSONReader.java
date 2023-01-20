package application.model;


import java.io.File;
import java.util.Scanner;
import application.control.ShowData;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;


/**
 * TODO
 */
public class JSONReader implements Runnable {

    public static final JSONReader instance = new JSONReader(); // TODO
    private boolean running; // TODO

    /**
     * Constructor
     */
    private JSONReader() {
        this.running = false;
    }

    /**
     * Initialize the JSONController
     */
    public void init() {}

    /**
     * Start the thread of the JSONReader
     */
    @Override
    public void run() {
        try {
            // Open data.json file
            File file = new File(System.getProperty("user.dir") + "/../../codePython/data.json");
            long lastModified = (file.exists()) ? file.lastModified() : 0;
            while (this.running) {
                // Check if the file exist
                if (file.exists()) {
                    if (lastModified != file.lastModified()) {
                        lastModified = file.lastModified();
                        // Read the file
                        Scanner scanner = new Scanner(file);
                        String content = "";
                        while(scanner.hasNextLine()) {
                            content += scanner.nextLine();
                        }
                        scanner.close();
                        // Send the data
                        this.sendData((JSONObject) new JSONParser().parse(content));
                    }
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Get the instance of JSONReader
     * @return JSONReader
     */
    public static JSONReader getInstance() {
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
    
    /**
     * Get the running state of the JSONReader
     * @return boolean
     */
    public boolean getRunning() {
        return this.running;
    }

    /**
     * Send the data to the thread
     * @param pfData
     */
    private void sendData(JSONObject pfData) {
        ShowData.getInstance().setData(pfData);
    }

}
