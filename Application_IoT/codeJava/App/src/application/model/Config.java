package application.model;


import java.io.File;
import java.io.FileNotFoundException;
import java.util.Scanner;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;
import application.control.DialogueController;


public class Config {

    private final static Config instance = new Config();
    private String[] keys;

    private DialogueController dialogueController;

    public void setDialogueController(DialogueController dialogueController) {
        this.dialogueController = dialogueController;
    }

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

    public static Config getInstance() {
        return instance;
    }

    public void loadConfig() {
        File file = new File(System.getProperty("user.dir") + "/Application_IoT/codePython/config.json");
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