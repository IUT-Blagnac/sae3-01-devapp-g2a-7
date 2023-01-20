package application.model;


import java.io.File;
import java.io.FileNotFoundException;
import java.util.Scanner;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;


/**
 * TODO
 */
import application.control.DialogueController;

public class Config {

    private final static Config instance = new Config(); // TODO
    private String[] keys; // TODO
    private DialogueController dialogueController; // TODO

    /**
     * TODO
     * @param dialogueController
     */
    public void setDialogueController(DialogueController dialogueController) {
        this.dialogueController = dialogueController;
    }

    /**
     * TODO
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
    }

    /**
     * // TODO
     * @return
     */
    public static Config getInstance() {
        return instance;
    }

    /**
     * TODO
     */
    public void loadConfig() {
        File file = new File(System.getProperty("user.dir") + "/../../codePython/config.json");
        if (file.exists()) {
            try {
                Scanner scanner = new Scanner(file);
                String content = "";
                while (scanner.hasNextLine()) {
                    content += scanner.nextLine();
                }
                scanner.close();
                this.dialogueController.loadView((JSONObject) new JSONParser().parse(content));
                JSONWriter.getInstance().setDataToCollect((JSONObject) new JSONParser().parse(content));

            } catch (FileNotFoundException | ParseException e) {
                System.out.println("Erreur impossible, un vrai problème est survenu ailleurs");
                System.out.println(e.getMessage());
                System.exit(1);
            }
        } else {
            JSONWriter.getInstance().setDonneesByDefault(keys);
            JSONWriter.getInstance().setSeuilsByDefault(keys);
            JSONWriter.getInstance().writeData();
            this.dialogueController.loadView(JSONWriter.getInstance().getDataToCollect());
        }
    }

}
