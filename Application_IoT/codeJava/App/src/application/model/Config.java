package application.model;


import java.io.File;
import java.io.FileNotFoundException;
import java.util.Scanner;
import javafx.scene.control.Alert;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;
import application.control.DialogueController;


/**
 * Reload the config file.
 */
public class Config {

    private final static Config instance = new Config(); // Singleton
    private String[] keys; // Store all keys
    private DialogueController dialogueController; // Store the dialogueController
    private String content; // Store the content of the config file

    /**
     * setter for dialogueController
     * @param dialogueController
     */
    public void setDialogueController(DialogueController dialogueController) {
        this.dialogueController = dialogueController;
    }

    /**
     * Constructor
     */
    private Config() {
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
        this.content = "";
    }

    /**
     * getter for instance
     * @return
     */
    public static Config getInstance() {
        return instance;
    }

    /**
     * Initialize the content of the config file
     */
    public void init() {
        String path = System.getProperty("user.dir").replace("codeJava\\App", "codePython\\config.json"); // TO MODIFY
        File file = new File(path);
        if (file.exists()) {
            try {
                Scanner scanner = new Scanner(file);
                while (scanner.hasNextLine()) {
                    this.content += scanner.nextLine();
                }
                scanner.close();
            } catch (FileNotFoundException e) {
                System.out.println("Un problème est survenu.");
                System.out.println(e.getMessage());
                System.exit(1);
            }
        } else {
            this.content = "";
        }
    }

    /**
     * Reset the config file with the default configuration
     */
    public void resetConfig() {
        this.content = "";
        this.loadConfig();
    }

    /**
     * Load the config and set the view
     */
    public void loadConfig() {
        if (this.content.equals("")) {
            JSONWriter.getInstance().setDonneesByDefault(keys);
            JSONWriter.getInstance().setSeuilsByDefault(keys);
            JSONWriter.getInstance().writeData();
            this.dialogueController.loadView(JSONWriter.getInstance().getDataToCollect());
        } else {
            try {
                this.dialogueController.loadView((JSONObject) new JSONParser().parse(this.content));
                JSONWriter.getInstance().setDataToCollect((JSONObject) new JSONParser().parse(this.content));
            } catch (ParseException e) {
                // Popup showing an error
                Alert alert = new Alert(Alert.AlertType.ERROR);
                alert.setTitle("Erreur");
                alert.setHeaderText("ERREUR :");
                alert.setContentText("Un problème est survenu lors du chargement de la configuration.");
                alert.showAndWait();
                System.exit(1);
            }
        }
    }

}
