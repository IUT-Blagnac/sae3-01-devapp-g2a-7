package application.model;


import java.io.File;
import java.util.Scanner;
import application.control.ShowData;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;


/**
 * Read the data.json file and send the data to the ShowData class
 */
public class JSONReader implements Runnable {

    public static final JSONReader instance = new JSONReader(); // Singleton
    private boolean running; // Store the state of the thread

    /**
     * Constructor
     */
    private JSONReader() {
        this.running = false;
    }

    /**
     * Start the thread of the JSONReader
     */
    @Override
    public void run() {
        try {
            // Open data.json file
            String path = "data.json"; //System.getProperty("user.dir").replace("codeJava\\App", "codePython\\data.json"); // TO MODIFY
            File file = new File(path);
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
     * @return JSONReader instance
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
     * @return boolean running
     */
    public boolean getRunning() {
        return this.running;
    }

    /**
     * Send the data to the thread
     * @param pfData JSONObject data
     */
    private void sendData(JSONObject pfData) {
        ShowData.getInstance().setData(pfData);
    }

}
