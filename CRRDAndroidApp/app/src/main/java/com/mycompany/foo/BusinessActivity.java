package com.mycompany.foo;

import android.app.ListActivity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleAdapter;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Mollie on 6/9/2015.
 */
//help from http://www.androidhive.info/2012/01/android-json-parsing-tutorial/
public class BusinessActivity extends ListActivity {
    public final static String IND_BUSINESS_MESSAGE = "com.mycompany.foo.IND_BUSINESS_MESSAGE";
    private static final String DEBUG_TAG = "HttpExample";
    Context cntxt = this;
    JSONArray businesses = null;
    String message;

    // Hashmap for ListView
    ArrayList<HashMap<String, String>> businessesList;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_businesses);
        // Get the message from the intent
        Intent intent = getIntent();
        message = intent.getStringExtra(ItemsActivity.BUSINESS_MESSAGE);

        businessesList = new ArrayList<HashMap<String, String>>();

        if(message != null) {
            try {

                JSONObject jsonObj = new JSONObject(message);

                businesses = jsonObj.getJSONArray("businesses");

                // looping through All categories
                for (int i = 0; i < businesses.length(); i++) {
                    JSONObject bus = businesses.getJSONObject(i);
                    JSONObject app = bus.getJSONObject("application");

                    String name = bus.getString("name");
                    String address = bus.getString("address");
                    String phone = bus.getString("phone");
                    String website = bus.getString("website");
                    String hours = bus.getString("hours");
                    String info = bus.getString("info");
                    String reuse = app.getString("reuse");
                    String repair = app.getString("repair");

                    // tmp hashmap for single contact
                    HashMap<String, String> business = new HashMap<String, String>();

                    // adding each child node to HashMap key => value
                    business.put("name", name);
                    business.put("address", address);
                    business.put("phone", phone);
                    business.put("hours", hours);
                    business.put("website", website);
                    business.put("info", info);

                    if(reuse.equals("1") && repair.equals("1")){
                        business.put("repair-reuse", "Reuse Repair");
                    }
                    else if(reuse.equals("1") && repair.equals("0")){
                        business.put("repair-reuse", "Reuse");
                    }
                    else if(reuse.equals("0") && repair.equals("1")){
                        business.put("repair-reuse", "Repair");
                    }
                    else if(reuse.equals("0") && repair.equals("0")){
                        business.put("repair-reuse", "");
                    }

                    // adding art to artwork list
                    businessesList.add(business);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

        final ListAdapter adapter = new SimpleAdapter(
                BusinessActivity.this, businessesList,
                R.layout.business_item, new String[] { "name", "address", "phone", "repair-reuse"},
                new int[] { R.id.name, R.id.address,  R.id.phone, R.id.repair_reuse});

        setListAdapter(adapter);

        ListView lv = getListView();

        lv.setOnItemClickListener(new AdapterView.OnItemClickListener() {

            @Override
            public void onItemClick(AdapterView<?> parent, View view,
                                    int position, long id) {

                HashMap<String, String> listItem = (HashMap<String, String>) businessesList.get(position);
                String name = (String) listItem.get("name");
                String address = (String) listItem.get("address");
                String phone = (String) listItem.get("phone");
                String hours = (String) listItem.get("hours");
                String website = (String) listItem.get("website");
                String info = (String) listItem.get("info");
                String rr = (String) listItem.get("repair-reuse");

                StringBuilder businessbuild = new StringBuilder();
                String businessmessage = businessbuild.append("{\"name\":\"").append(name)
                        .append("\",\"address\":\"").append(address)
                        .append("\",\"phone\":\"").append(phone)
                        .append("\",\"hours\":\"").append(hours)
                        .append("\",\"website\":\"").append(website)
                        .append("\",\"info\":\"").append(info)
                        .append("\",\"rr\":\"").append(rr)
                        .append("\"}").toString();

                Intent intent = new Intent(cntxt, IndividualBusinessActivity.class);
                intent.putExtra(IND_BUSINESS_MESSAGE, businessmessage);
                startActivity(intent);
            }
        });
    }

}
